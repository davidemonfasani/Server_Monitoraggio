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
            if ($decoded_jwt) {
               
                if (Carbon::now()->timestamp > $decoded_jwt->iat && Carbon::now()->timestamp < $decoded_jwt->exp) { //controlla che il token sia valido
                    return $this->retriveCellars($decoded_jwt); //prende le cantine con i dati del token
                } else {
                    return response()->json(['error' => 'session expired'], 403);
                }
            } else {
                return response()->json(['error' => 'invalid token'], 401);
            }
        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function CellarsMoniINFO(Request $request)   {
        try {
            $request->validate([
                'token' => [
                    'required'
                ],
                'id_cellar'=>[
                    'required',
                    'exists:Cellars,id_cellar',
                ]

            ]);
            $users=Cellar::where('id_cellar', $request->id_cellar)->first()->users;
            $user = User::where('id_user', $decoded_jwt->id)->first();

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    private function retriveCellars($decoded_jwt)
    {
        $user = User::where('id_user', $decoded_jwt->id)->first();
    
        if ($user) {
            $cantine = $user->cellars; //prende le cantine associate all'utente    
            return response()->json($cantine);
        } else {
            return response()->json(['error' => 'User Not Found'], 404);
        }
    }
    
}
