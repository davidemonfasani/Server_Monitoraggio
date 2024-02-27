<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;

trait UserAuthTrait {

    private function generateJWT($data)
    {
        $key = strval(env('JWT_KEY'));
        $exp_time = 5; //Days

        $payload = array(
            "id" => $data->id,
            'nome' => $data->nome,
            'cognome' => $data->cognome,
            'email' => $data->email,
            "iat" => Carbon::now()->timestamp,
            "exp" => Carbon::now()->addDays($exp_time)->timestamp,
        );

        $jwt = JWT::encode($payload, $key, 'HS256');

        return $jwt;
    }

    private function decodeJWT($jwt)
    {
        $key = env('JWT_KEY');
        try {
            $decoded_jwt = JWT::decode($jwt, $key, ['HS256']);
            return $decoded_jwt;
    
        } catch (\Exception $e) {
            return null;
        }
    }
    
    

    //controlla se il token è valido

    public function checkLogged(Request $request)
    {
        try {
            $request->validate(['token'=>'required']);
            $jwt = $request->token;
            if ($this->decodeJWT($jwt)) {
                $decoded_jwt = $this->decodeJWT($jwt);

                if (Carbon::now()->timestamp > $decoded_jwt->iat && Carbon::now()->timestamp < $decoded_jwt->exp) {
                    return response()->json($jwt);
                } else {
                    return response()->json(['error' => 'session expired'], 403);
                }
            } else {
                return response()->json(['error' => 'invalid token'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
    
}
