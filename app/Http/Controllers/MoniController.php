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
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;


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
            ]);
        }
        catch (ValidationException $e) {

            $id_cellar = \App\Models\Sensor::where('id_Sensor', '=', $request->id_Sensor)
            ->select('id_cellar')
            ->first();

            $assCellar=\App\Models\AssCellar::where('id_cellar', '=', $id_cellar->id_cellar)->get();


            foreach ($assCellar as $assCellar) {
                $user = User::where('id_user','=',$assCellar->id_user)->first();
                if ($user) {
                    $email = $user->email;
                    Mail::to($email)->send(new MoniError($e->getMessage()));
                    return response()->json([
                         $id_cellar,
                        'id_user'=>$user,
                        $email
                    ]);
                }
            }


            throw $e;
        }
        catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }




}
