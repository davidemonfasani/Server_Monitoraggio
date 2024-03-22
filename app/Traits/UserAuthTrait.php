<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;

use function Laravel\Prompts\error;

trait UserAuthTrait {

    private function generateJWT($data)
    {
        $key = env('JWT_SECRET');
        if (empty($key)) {
            throw new \Exception('JWT key is empty generateJWT');
        }

        $exp_time = 5; // Days

        $payload = array(
            "id" => $data->id_user,
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
        $key = env('JWT_SECRET');
        if (empty($key)) {
            throw new \Exception('JWT key is empty decodeJWT', $key);
        }

        try {
            $key = new Key($key, 'HS256');
            $decoded_jwt = JWT::decode($jwt, $key);

            return $decoded_jwt;

        }  catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
        catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }



}
