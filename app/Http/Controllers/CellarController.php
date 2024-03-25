<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AssCellar;
use App\Models\Cellar;
use App\Models\User;
use App\Traits\UserAuthTrait;
use Illuminate\Support\Carbon;

class CellarController extends Controller
{
    use UserAuthTrait;
    public function store(Request $request)
    {
        try {
            $request->validate([
                'token' => [
                    'required'
                ],
                'nome' => ['required', 'string', 'max:35', 'unique:cellars,nome'],
                'citta' => ['required', 'string', 'max:35'],
                'provincia' => ['required', 'string', 'max:35'],
                'via' => ['required', 'string', 'max:35'],
                'n_civico' => ['required', 'integer'],
                'dimensioneMq' => ['required', 'integer'],
                'numero_sensori' => ['integer'],
            ]);
            $decoded_jwt = $this->decodeJWT($request->token);
            if (isset($decoded_jwt->exp)) //controlla se è  un token
            {
                $cellar = new Cellar();
                $cellar->nome = $request->nome;
                $cellar->citta = $request->citta;
                $cellar->provincia = $request->provincia;
                $cellar->via = $request->via;
                $cellar->n_civico = $request->n_civico;
                $cellar->dimensioneMq = $request->dimensioneMq;
                $cellar->numero_sensori = $request->numero_sensori ?? 0;
                $cellar->save();
                $assCellar = new AssCellar();
                $assCellar->id_cellar = $cellar->id_cellar;
                $assCellar->id_user = $decoded_jwt->id;
                $assCellar->save();
                return response()->json(['Nuova cantina creata:' => $cellar, 'Associazione Utenti' => $assCellar]);
            } else {
                return $decoded_jwt;
            }
        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function AssCellar(Request $request) //permette all'utente di associare ad una canti
    {
        try {

            $request->validate([
                'token' => [
                    'required'
                ],
                'id_cellar' => ['required', 'integer', 'max:35', 'exists:cellars,id_cellar'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:55',
                    'exists:Users,email',
                ],
            ]);
            $decoded_jwt = $this->decodeJWT($request->token);
            if (isset($decoded_jwt->exp)) //controlla se è  un token
            {
                if ($request->email !== $decoded_jwt->email) {
                    $cellar = Cellar::where('id_cellar', $request->id_cellar)->first();
                        $users = $cellar->users;
                        if ($users->contains('id_user', $decoded_jwt->id)) {//se l'utente che ha fatto la rischiesta è associato alla cantina
                            $newUser = User::where('email', $request->email)->first();
                            if (!$users->contains('id_user', $newUser->id_user)) {//se l'utente che deve essere associato alla cantina, non è già stato associato
                                $assCellar = new AssCellar();
                                $assCellar->id_cellar = $cellar->id_cellar;
                                $assCellar->id_user = $newUser->id_user;
                                $assCellar->save();//lo associa
                                $users = $cellar->users;
                                return response()->json(['Associazione di un nuovo utente alla cantina avvenuta' => $cellar, 'assCellar' => $assCellar]);
                            } else {
                                return response()->json(['error' =>'Associazione Già avvenuta per questo utente']);
                            }
                        } else {
                            return response()->json(['error' => 'non autorizzato per questa cantina'], 401);
                        }

                } else {
                    return response()->json(['error' => 'Non puoi auto associarti una cantina'], 400);
                }
            } else {
                return $decoded_jwt;
            }
        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function retrive_cellars(Request $request)
    {
        try {

            $request->validate([
                'token' => [
                    'required'
                ]
            ]);


            $jwt = $request->token;


            $decoded_jwt = $this->decodeJWT($jwt);

            if (isset($decoded_jwt->exp)) //controlla se è  un token
            {
                return $this->retriveCellars($decoded_jwt);
            } else {
                return $decoded_jwt;
            }
        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function CellarsSensors(Request $request)
    {
        try {
            $request->validate([
                'token' => [
                    'required',

                ],
                'id_cellar' => [
                    'required',
                    'exists:Cellars,id_cellar',
                ]

            ]);
            $decoded_jwt = $this->decodeJWT($request->token);

            if (isset($decoded_jwt->exp)) {
                $cellar = Cellar::where('id_cellar', $request->id_cellar)->first();

                if ($cellar) {
                    $users = $cellar->users;
                    if ($users->contains('id_user', $decoded_jwt->id)) {
                        $cellarSensors = Cellar::where('id_cellar', $request->id_cellar)->first()->sensors;
                        return response()->json(['cellarInfo' => $cellarSensors], 200);
                    } else {
                        return response()->json(['error' => 'non autorizzato per questa cantina'], 401);
                    }
                } else {
                    return response()->json(['error' => 'cantina non trovata'], 404);
                }
            } else {
                return $decoded_jwt;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    private function retriveCellars($decoded_jwt)
    {
        $user = User::where('id_user', $decoded_jwt->id)->first();
        if ($user) {
            $cantine = $user->cellars; //prende le cantine associate all'utente
            return response()->json(['Cellars' => $cantine], 200);
        } else {
            return response()->json(['error' => 'User Not Found'], 404);
        }
    }
}
