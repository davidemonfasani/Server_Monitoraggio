<?php

namespace App\Http\Controllers;
use App\Models\Monitoraggio;
use App\Models\Sensor;
use App\Models\User;
use App\Models\AssCellar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Mail\MoniError;

class MoniController extends Controller

{


    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_Sensor'=> [
                    'required',
                    'integer',
                    'exists:Sensors,id_Sensor',
                ],
                'Temperatura'=> [
                    'required',
                    'integer',
                ],
                'Umidità'=> [
                    'required',
                    'integer',
                ],
                'Peso'=> [
                    'required',
                    'integer',
                ],
            ],
            [//messaggi di errore personalizzati
                'id_Sensor.required' => 'Sensore non presente nel database',
                'Temperatura.required' => 'Valore della temperatura mancante',
                'Umidità.required' => 'Valore della Umidità mancante',
                'Peso.required' => 'Valore della Peso mancante',
            ]);
            $recordMoni = new Monitoraggio();
            $recordMoni->id_Sensor=$request->id_Sensor;
            $recordMoni->Umidità=$request->Umidità;
            $recordMoni->Temperatura=$request->Temperatura;
            $recordMoni->Peso=$request->Peso;
            $recordMoni->save();


            $sensor = \App\Models\Sensor::where('id_Sensor', '=', $request->id_Sensor)->first();
            $cellar = \App\Models\Cellar::where('id_cellar', '=', $sensor->id_cellar)->first();

            $message = '';

            if ($request->Temperatura > $sensor->{'Temperatura-Max'}) {
                $message .= "Temperatura eccessiva sul sensore N {$sensor->id_Sensor} della cantina {$cellar->nome}. ".`\n`;
                $obj='Temperatura eccessiva';
            } elseif ($request->Temperatura < $sensor->{'Temperatura-Min'}) {
                $message .= "Temperatura insufficiente sul sensore N {$sensor->id_Sensor} della cantina {$cellar->nome}. ".`\n`;
                $obj='Temperatura insufficiente';
            }

            if ($request->Umidità > $sensor->{'Umidità-Max'}) {
                $message .= "Umidità eccessiva sul sensore N {$sensor->id_Sensor} della cantina {$cellar->nome}. ";
                $obj.='Umidità eccessiva';
            } elseif ($request->Umidità < $sensor->{'Umidità-Min'}) {
                $message .= "Umidità insufficiente sul sensore N {$sensor->id_Sensor} della cantina {$cellar->nome}. ";
                $obj.='Umidità insufficiente';
            }

            if (!empty($message)) {
                $assCellar = \App\Models\AssCellar::where('id_cellar', '=', $cellar->id_cellar)->get();
                foreach ($assCellar as $assCellar) {
                    $user = User::where('id_user','=',$assCellar->id_user)->first();
                    MoniController::sendEmail($user, $message, $obj);
                }
            }

            return response()->json('Messaggio monitoraggio avvenuto');

        }
        catch (ValidationException $e) {

            $id_cellar = \App\Models\Sensor::where('id_Sensor', '=', $request->id_Sensor)
            ->select('id_cellar')
            ->first();

            $assCellar=\App\Models\AssCellar::where('id_cellar', '=', $id_cellar->id_cellar)->get();


            $errors = $e->errors(); // prende tutti gli errori

            // e gli converte in una sola stringa
            $errorMessage = '';
            foreach ($errors as $field => $messages) {
                $errorMessage .= $field . ': ' . implode(', ', $messages) . "\n";
            }

            foreach ($assCellar as $assCellar) {
                $user = User::where('id_user','=',$assCellar->id_user)->first();
                MoniController::sendEmail($user,$errorMessage, 'Errori Da un sensore');
                }
                return response()->json([
                    $errors
                ], 400);


            throw $e;
        }
        catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    private function sendEmail($user, $message, string $obj)
    {

            if ($user) {
                $email = $user->email;
                Mail::to($email)->send(new MoniError($message, $obj)); //che verra inviata come messaggio di una mail a tutti gli utenti associati alla cantina
            }
        }
}
