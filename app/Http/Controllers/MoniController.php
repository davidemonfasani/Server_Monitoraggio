<?php

namespace App\Http\Controllers;

use App\Models\Monitoraggio;
use App\Models\Sensor;
use App\Models\User;
use App\Models\Cellar;
use App\Models\AssCellar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Traits\EmailTrait;



class MoniController extends Controller
{
    use EmailTrait;

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'id_sensor' => [
                        'required',
                        'integer',
                        'exists:Sensors,id_sensor',
                    ],
                    'Temperatura' => [
                        'required',
                        'integer',
                    ],
                    'Umidita' => [
                        'required',
                        'integer',
                    ],
                    'Peso' => [
                        'required',
                        'integer',
                    ],
                ],
                [//messaggi di errore personalizzati
                    'id_sensor.required' => 'Sensore non presente nel database',
                    'Temperatura.required' => 'Valore della temperatura mancante',
                    'Umidita.required' => 'Valore della Umidita mancante',
                    'Peso.required' => 'Valore della Peso mancante',
                ]
            );
            $recordMoni = new Monitoraggio();
            $recordMoni->id_sensor = $request->id_sensor;
            $recordMoni->Umidita = $request->Umidita;
            $recordMoni->Temperatura = $request->Temperatura;
            $recordMoni->Peso = $request->Peso;
            $recordMoni->save();


            $sensor = Sensor::where('id_sensor', '=', $request->id_sensor)->first();
            $sensor->TemperaturaNow=$recordMoni->Temperatura;
            $sensor->UmiditaNow=$recordMoni->Umidita;
            $sensor->PesoNow=$recordMoni->Peso;
            $sensor->save();//salva la temperatura corrente sul db
            $cellar = $sensor->cellar;

            $message = '';

            if ($request->Temperatura > $sensor->TemperaturaMax) {
                $message .= "Temperatura eccessiva sul sensore N {$sensor->id_sensor} della cantina: {$cellar->nome}. " .  "\n";
                $obj = 'Temperatura eccessiva';
            } elseif ($request->Temperatura < $sensor->TemperaturaMin) {
                $message .= "Temperatura insufficiente sul sensore N {$sensor->id_sensor} della cantina {$cellar->nome}. " .  "\n";
                $obj = 'Temperatura insufficiente';
            }

            if ($request->Umidita > $sensor->UmiditaMax) {
                $message .= "Umidita eccessiva sul sensore N {$sensor->id_sensor} della cantina {$cellar->nome}. ";
                $obj .= 'Umidita eccessiva';
            } elseif ($request->Umidita < $sensor->UmiditaMin) {
                $message .= "Umidita insufficiente sul sensore N {$sensor->id_sensor} della cantina {$cellar->nome}. ";
                $obj .= 'Umidita insufficiente';
            }

            if (!empty($message)) {
                $users = $cellar->users;//prende gli utenti associati alla cantina del sensore
                foreach ($users as $user) {
                    $this->sendEmail($user, $message, $obj);
                    return response()->json(['ERROR'=>$message]);
                }
            } else {
                return response()->json('Messaggio monitoraggio avvenuto');
            }


        } catch (ValidationException $e) {

            $users = Sensor::where('id_sensor', '=', $request->id_sensor)->first()->cellar->users;//prende gli utenti associati alla cantina del sensore
            $errors = $e->errors(); // prende tutti gli errori

            // e gli converte in una sola stringa
            $errorMessage = '';
            foreach ($errors as $field => $messages) {
                $errorMessage .= $field . ': ' . implode(', ', $messages) . "\n";
            }
            foreach ($users as $user) {
                $this->sendEmail($user, $errorMessage, 'Errori Da un sensore');
            }
            return response()->json([
                $errors
            ], 400);
        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}
