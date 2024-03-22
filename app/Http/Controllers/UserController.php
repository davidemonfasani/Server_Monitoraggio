<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\key;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\UserAuthTrait;


class UserController extends Controller
{
    use UserAuthTrait;
    public function store(Request $request)
    {
        try {
            $request->validate([
                'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'nome' => 'required|string|max:35',
                'cognome' => 'required|string|max:35',
                'email' => 'required|string|email|max:55|unique:Users,email',
                'password' => 'required|string|max:300',
            ]);
            $user = new User();
            if (!$request->foto == null) {
                $imageName = $request->email . date('Y-m-d-H-i-s') . '.' . $request->foto->extension();
                $request->foto->move(public_path('images/pfp'), $imageName);
                $user->foto = 'images/pfp/' . $imageName;
            }
            $user->nome = $request->nome;
            $user->cognome = $request->cognome;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);//salva solo l'hash della passward
            $user->save();
            $jwt = $this->generateJWT($user);

            error_log('Generated JWT: ' . $jwt);
            $response = [
                'token' => $jwt,
                "message" => 'Registrazione Avvenuta!',
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    public function Login(Request $request)
    {
        try {

            $request->validate([
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:55',
                    'exists:Users,email',
                ],
                'password' => [
                    'required',
                    'string',
                    'max:300',
                    //questa funzione controlla se la passward è uguale a quella che è salvata nel database
                    function ($attribute, $value, $fail) use ($request) {
                        $user = User::where('email', $request->email)->first();
                        if ($user && !Hash::check($request->password, $user->password)) {//confrota l'hash di quella della richiesta
                            $fail('Email or password incorrect.');//manda un messaggio i errore nella validazione della richiesta
                        }
                    }
                ],
            ]);
            $user = User::where('email', $request->email)->first();//prende il primo utente con quella password quindi l'unico dato che è unique

            $jwt = $this->generateJWT($user);
            error_log('Generated JWT: ' . $jwt);
            $response = [
                'token' => $jwt,
                "message" => 'Login Avvenuto!',
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email|max:55',
                'old_password' => 'required|string|max:300',
                'new_password' => 'required|string|max:300|different:old_password',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->old_password, $user->password)) {
                return response()->json(['error' => 'Invalid email or password'], 404);
            }
            else{
                $user->password = Hash::make($request->new_password);
                $user->save();

                $jwt = $this->generateJWT($user);

                error_log('Generated JWT: ' . $jwt);
                $response = [
                    'token' => $jwt,
                    "message" => 'Password updated successfully!',
                ];

                return response()->json($response, 200);
            }


        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'request.email' => $request->email
            ], 404);
        }
    }


    public function checkLogged(Request $request)
    {
        return $this->decodeJWT($request->token);
    }

}
