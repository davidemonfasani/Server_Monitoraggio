<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Models\Monitoraggio;
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
                'UmiditaMax' => ['required', 'numeric'],
                'TemperaturaMin' => ['required', 'numeric'],
                'UmiditaMin' => ['required', 'numeric'],
                'Timer' => ['required', 'integer'],
            ]);
            $decoded_jwt = $this->decodeJWT($request->token);
            if (isset($decoded_jwt->exp)) //controlla se è un token valido
            {
                $users = Cellar::where('id_cellar', $request->id_cellar)->first()->users;
                if ($users->contains('id_user', $decoded_jwt->id)) { //se l'utente che ha fatto la rischiesta è associato alla cantina
                    $sensor = new Sensor();
                    $sensor->id_cellar = $request->id_cellar;
                    $sensor->TemperaturaMax = $request->TemperaturaMax;
                    $sensor->UmiditaMax = $request->UmiditaMax;
                    $sensor->TemperaturaMin = $request->TemperaturaMin;
                    $sensor->UmiditaMin = $request->UmiditaMin;
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
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function update(Request $request)
    {
        try {

            $request->validate([ //aggiungere cambiamenti sulle validazioni
                'id_sensor' => ['required', 'exists:sensors,id_sensor'],
                'token' => ['required'],
                'id_cellar' => ['required', 'integer', 'exists:cellars,id_cellar'],
                'TemperaturaMax' => ['sometimes', 'required', 'numeric'],
                'UmiditaMax' => ['sometimes', 'required', 'numeric', function ($attribute, $value, $fail) {
                    if ($value <= 0 ) {
                        $fail('UmiditaMax must be greater than 0.');
                    }
                    else  if ($value > 100 ) {
                        $fail('UmiditaMax must be lower than 100.');
                    }
                }],
                'TemperaturaMin' => ['sometimes', 'required', 'numeric'],
                'UmiditaMin' => ['sometimes', 'required', 'numeric', function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('UmiditaMin must be greater than 0.');
                    }  else  if ($value > 100 ) {
                        $fail('UmiditaMin must be lower than 100.');
                    }
                }],
                'Timer' => ['sometimes', 'required', 'integer', function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('Timer must be greater than 0.');
                    }
                }],
            ]);


            $sensor = Sensor::where('id_sensor', $request->id_sensor)->first();
            $decoded_jwt = $this->decodeJWT($request->token);
            if (isset($decoded_jwt->exp)) {
                $errormsg="";
                $users = Cellar::where('id_cellar', $request->id_cellar)->first()->users;
                if ($users->contains('id_user', $decoded_jwt->id)) {
                    if ($request->has('TemperaturaMax')) {
                        $sensor->TemperaturaMax = $request->TemperaturaMax;
                    }
                    if ($request->has('UmiditaMax')) {
                        $sensor->UmiditaMax = $request->UmiditaMax;
                    }
                    if ($request->has('TemperaturaMin')) {
                        $sensor->TemperaturaMin = $request->TemperaturaMin;
                    }
                    if ($request->has('UmiditaMin')) {
                        $sensor->UmiditaMin = $request->UmiditaMin;
                    }
                    if ($request->has('Timer')) {
                        $sensor->Timer = $request->Timer;
                    }
                    if($sensor->TemperaturaMax<=$sensor->TemperaturaMin)
                    {
                        $errormsg='Temperatura minima maggiore o uguale alla massima';
                    }
                    if($sensor->UmiditaMax<=$sensor->UmiditaMin){
                        $errormsg= `\nUmidita minima maggiore o uguale alla massima`;
                    }
                    if($errormsg=="")
                    {
                        $sensor->save();
                        return response()->json(['Sensore aggiornato' => $sensor], 200);
                    }
                    else
                    {
                        return response()->json(['error' => $errormsg], 400);
                    }
                } else {
                    return response()->json(['error' => 'Non autorizzato per questa cantina'], 401);
                }
            } else {
                return $decoded_jwt;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function RetriveInfo(Request $request)//serve per configurare il sensore fisico
    {
        try {

            $request->validate([
                'id_cellar' => ['required', 'integer', 'exists:cellars,id_cellar'],
                'id_sensor' => ['required', 'integer', 'exists:sensors,id_sensor'],
            ]);
            $sensors=Sensor::where('id_cellar', $request->id_cellar)->get();
            if($sensors->contains('id_sensor', $request->id_sensor))//cotrollas se è associato a quella catina
            {
                $sensor = Sensor::where('id_sensor', $request->id_sensor)->first();
            return response()->json(['Parametri del sensore' => $sensor]);
            } else {
                return response()->json(['error' => 'sensor not associated to this cellar'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function GetMoniReports(Request $request)//serve per configurare il sensore fisico
    {
        try {

            $request->validate([
                'id_cellar' => ['required', 'integer', 'exists:cellars,id_cellar'],
                'id_sensor' => ['required', 'integer', 'exists:sensors,id_sensor'],
            ]);
            $sensors=Sensor::where('id_cellar', $request->id_cellar)->get();
            
            if($sensors->contains('id_sensor', $request->id_sensor))//cotrollas se è associato a quella catina
            {
                $reports = Monitoraggio::where('id_sensor', $request->id_sensor)->get();
            return response()->json(['reports del sensore' => $reports]);
            } else {
                return response()->json(['error' => 'sensor not associated to this cellar'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
