<?php

namespace App\Http\Controllers;
use App\Models\Monitoraggio;
use App\Models\Sensor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
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
            
            $id_cellar = \App\Models\Sensor::where('id_Sensor', '=', '1')
            ->select('id_cellar')
            ->get();

            $emails = \App\Models\User::join('ass_cellars', 'ass_cellars.id_user', '=', 'users.id_user')
            ->join('cellars', 'cellars.id_cellar', '=', 'ass_cellars.id_cellar')
            ->where('cellars.id_cellar', '=', $id_cellar)
            ->select('users.email')
            ->get();

            foreach ($emails as $mails) {
                Mail::to($mail)->send(new ValidationError($e->errors()));
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
