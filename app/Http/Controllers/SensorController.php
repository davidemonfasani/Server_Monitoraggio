<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Models\Cellar;
use Illuminate\Support\Facades\Log;
use App\Traits\UserAuthTrait;


class SensorController extends Controller
{
    use UserAuthTrait;
    public function store(Request $request)
    {
        try {

            $request->validate([
                'token' => [
                    'required'
                ],
                'id_cellar' => ['required', 'integer', 'exists:cellars,id_cellar'],
                'TemperaturaMax' => ['required', 'numeric'],
                'UmiditàMax' => ['required', 'numeric'],
                'TemperaturaMin' => ['required', 'numeric'],
                'UmiditàMin' => ['required', 'numeric'],
                'Timer' => ['required', 'integer'],
            ]);
            $decoded_jwt = $this->checkValidToken($request->token);
            if (isset($decoded_jwt->exp)) //controlla se è un token valido
            {
                $users = Cellar::where('id_cellar', $request->id_cellar)->first()->users;
                if ($users->contains('id_user', $decoded_jwt->id)) {//se l'utente che ha fatto la rischiesta è associato alla cantina  
                    $sensor = new Sensor();
                    $sensor->id_cellar = $request->id_cellar;
                    $sensor->TemperaturaMax = $request->TemperaturaMax;
                    $sensor->UmiditàMax = $request->UmiditàMax;
                    $sensor->TemperaturaMin = $request->TemperaturaMin;
                    $sensor->UmiditàMin = $request->UmiditàMin;
                    $sensor->Timer = $request->Timer;
                    $sensor->save();
                    return response()->json(['Sensore aggiunto alla cantina' => $sensor]);
                } else {
                    return response()->json(['error' => 'Non autorizzato per questa cantina'], 401);
                }
            } else {
                return $decoded_jwt;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    protected $fillable = ['id_cellar', 'TemperaturaMax', 'UmiditàMax', 'TemperaturaMin', 'UmiditàMin', 'Timer'];
    public function update(Request $request)
    {
        try {
            $sensor = Sensor::where('id_sensor', $request->id_sensor)->first();
            $request->validate([//aggiungere cambiamenti sulle validazioni
                'id_sensor' => ['required', 'exists:sensors,id_sensor'],
                'token' => ['required'],
                'id_cellar' => ['required', 'integer', 'exists:cellars,id_cellar'],
                'TemperaturaMax' => ['sometimes', 'required', 'numeric', function ($attribute, $value, $fail) {
                    if ($value <= request('TemperaturaMin') ) {
                        $fail('TemperaturaMax must be greater than TemperaturaMin.');
                    }
                }],
                'UmiditàMax' => ['sometimes', 'required', 'numeric', function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('UmiditàMax must be greater than 0.');
                    }
                }],
                'TemperaturaMin' => ['sometimes', 'required', 'numeric'],
                'UmiditàMin' => ['sometimes', 'required', 'numeric', function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('UmiditàMin must be greater than 0.');
                    }
                }],
                'Timer' => ['sometimes', 'required', 'integer'],
            ]);
            
    
            
            $decoded_jwt = $this->checkValidToken($request->token);
            if (isset($decoded_jwt->exp)) {
                $users = Cellar::where('id_cellar', $request->id_cellar)->first()->users;
                if ($users->contains('id_user', $decoded_jwt->id)) {
                    if ($request->has('TemperaturaMax')) {
                        $sensor->TemperaturaMax = $request->TemperaturaMax;
                    }
                    if ($request->has('UmiditàMax')) {
                        $sensor->UmiditàMax = $request->UmiditàMax;
                    }
                    if ($request->has('TemperaturaMin')) {
                        $sensor->TemperaturaMin = $request->TemperaturaMin;
                    }
                    if ($request->has('UmiditàMin')) {
                        $sensor->UmiditàMin = $request->UmiditàMin;
                    }
                    if ($request->has('Timer')) {
                        $sensor->Timer = $request->Timer;
                    }
                    $sensor->save();
                    return response()->json(['Sensore aggiornato' => $sensor]);
                } else {
                    return response()->json(['error' => 'Non autorizzato per questa cantina'], 401);
                }
            } else {
                return $decoded_jwt;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    

}
