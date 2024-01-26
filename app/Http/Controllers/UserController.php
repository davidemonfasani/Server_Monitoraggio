<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\key;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function store(Request $request)
    {
    try {
        $request->validate([
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nome' => 'required|string|max:35',
            'cognome'=> 'required|string|max:35',
            'email'=> 'required|string|email|max:55|unique:Users,email',
            'password'=> 'required|string|max:300',
        ]);
        $user = new User();
        if(!$request->foto==null)
        {
            $imageName = $request->email.date('Y-m-d-H-i-s').'.'.$request->foto->extension();
            $request->foto->move(public_path('images/pfp'), $imageName);
            $user->foto = 'images/pfp/'.$imageName;
        }
        $user->nome = $request->nome;
        $user->cognome = $request->cognome;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);//salva solo l'hash della passward
        $user->save();
        $jwt = UserController::generateJWT($user);

        error_log('Generated JWT: ' . $jwt);
        $response=[
            'token' => $jwt,
            "message"=> 'Registrazione Avvenuta!',
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
        $user=User::where('email', $request->email)->first();//prende il primo utente con quella password quindi l'unico dato che è unique
        $jwt = UserController::generateJWT($user);
        error_log('Generated JWT: ' . $jwt);
        $response=[
            'token' => $jwt,
            "message"=> 'Login Avvenuto!',
        ];
        return response()->json($response);
    } catch (\Exception $e) {
        Log::error('Exception');
        Log::error($e->getMessage());
        return response()->json(['error' => $e->getMessage()], 404);
    }
    }




    private function generateJWT($data){
        $key = strval(env('JWT_KEY'));
        $exp_time = 5; //Days

        $payload = array(
            "id" => $data->id,
            'nome'=> $data->nome,
            'cognome' => $data->cognome,
            'email'=> $data->email,
            "iat" => Carbon::now()->timestamp,
            "exp" => Carbon::now()->addDays($exp_time)->timestamp,
        );

        $jwt = JWT::encode($payload, $key, 'HS256');

        return $jwt;
    }


    private function decodeJWT($jwt){
        $key = env('JWT_KEY');

        try
        {
            $decoded_jwt = JWT::decode($jwt, new Key($key, 'HS256'));
            return $decoded_jwt;

        }
        catch (\Exception $e) {
            return null;
     }}

    //controlla se il token è valido
    public function checkLogged(Request $request){
            try
            {
                $jwt = $request->token;
                if(UserController::decodeJWT($jwt))
                {
                    $decoded_jwt = UserController::decodeJWT($jwt);

                    if(Carbon::now()->timestamp > $decoded_jwt->iat && Carbon::now()->timestamp < $decoded_jwt->exp)
                    {
                        $jwt = UserController::generateJWT($decoded_jwt);
                        return response()->json($jwt);
                    }
                    else
                    {
                        return response()->json(['error' => 'session expired' ], 403);
                    }
                }
                else
                {
                    return response()->json(['error' => 'invalid token' ], 401);
                }
            }
            catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()],401);
            }
        }
}