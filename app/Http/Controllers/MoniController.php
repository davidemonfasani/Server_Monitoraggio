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
            ->get();
            $id_user=\App\Models\AssCellar::where('id_cellar', '=', $id_cellar)->get();
     
            \Log::info($id_user);
            
         
            $emails = User::join('Ass_cellars', 'Ass_cellars.id_user', '=', 'users.id_user')
            ->where('Ass_cellars.id_cellar', '=', $id_cellar)
            ->select('users.email')
            ->get();
            \Log::info($emails);

            foreach ($emails as $result) {
            echo $result->email;
            }


            return response()->json([
                'error' => $emails]);
            foreach ($emails as $mails) {
                Mail::to($mail)->send(new ValidationError($e->errors()));
            }

            return response()->json([
                'error' => $e->getMessage(),
                'emails'=> $emails,
            ], 404);
            throw $e;
        }
        catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    
    


}
