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
                    'id_Sensor' => [
                        'required',
                        'integer',
                        'exists:Sensors,id_Sensor',
                    ],
                    'Temperatura' => [
                        'required',
                        'integer',
                    ],
                    'Umidità' => [
                        'required',
                        'integer',
                    ],
                    'Peso' => [
                        'required',
                        'integer',
                    ],
                ],
                [//messaggi di errore personalizzati
                    'id_Sensor.required' => 'Sensore non presente nel database',
                    'Temperatura.required' => 'Valore della temperatura mancante',
                    'Umidità.required' => 'Valore della Umidità mancante',
                    'Peso.required' => 'Valore della Peso mancante',
                ]
            );
            $recordMoni = new Monitoraggio();
            $recordMoni->id_Sensor = $request->id_Sensor;
            $recordMoni->Umidità = $request->Umidità;
            $recordMoni->Temperatura = $request->Temperatura;
            $recordMoni->Peso = $request->Peso;
            $recordMoni->save();


            $sensor = Sensor::where('id_Sensor', '=', $request->id_Sensor)->first();
            $cellar = $sensor->cellar;

            $message = '';

            if ($request->Temperatura > $sensor->{'Temperatura-Max'}) {
                $message .= "Temperatura eccessiva sul sensore N {$sensor->id_Sensor} della cantina {$cellar->nome}. " . `\n`;
                $obj = 'Temperatura eccessiva';
            } elseif ($request->Temperatura < $sensor->{'Temperatura-Min'}) {
                $message .= "Temperatura insufficiente sul sensore N {$sensor->id_Sensor} della cantina {$cellar->nome}. " . `\n`;
                $obj = 'Temperatura insufficiente';
            }

            if ($request->Umidità > $sensor->{'Umidità-Max'}) {
                $message .= "Umidità eccessiva sul sensore N {$sensor->id_Sensor} della cantina {$cellar->nome}. ";
                $obj .= 'Umidità eccessiva';
            } elseif ($request->Umidità < $sensor->{'Umidità-Min'}) {
                $message .= "Umidità insufficiente sul sensore N {$sensor->id_Sensor} della cantina {$cellar->nome}. ";
                $obj .= 'Umidità insufficiente';
            }

            if (!empty($message)) {
                $users = $cellar->users;//prende gli utenti associati alla cantina del sensore
                foreach ($users as $user) {
                    $this->sendEmail($user, $message, $obj);
                }
            } else {
                return response()->json('Messaggio monitoraggio avvenuto');
            }


        } catch (ValidationException $e) {

            $users = Sensor::where('id_Sensor', '=', $request->id_Sensor)->first()->cellar->users;//prende gli utenti associati alla cantina del sensore
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
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

}
