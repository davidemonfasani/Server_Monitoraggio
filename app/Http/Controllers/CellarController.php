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
            return response()->json(['jwt' => $decoded_jwt], 403);
            if ($decoded_jwt) {
               
                return response()->json(['jwt' => $decoded_jwt], 403);
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

    private function retriveCellars($decoded_jwt)
    {
        $user = User::where('email', $decoded_jwt->id)->first();
        if (!$user) {
            $cellars = Cellar::where('id_user', $decoded_jwt->id)->get();
            response()->json($cellars);
        } else {
            return response()->json(['error' => 'User Not Found'], 404);
        }
    }
}
