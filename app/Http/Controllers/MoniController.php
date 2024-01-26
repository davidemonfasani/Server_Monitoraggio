<?php

namespace App\Http\Controllers;
use App\Models\Monitoraggio;
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
            $userEmail = Sensor::find($request->id_Sensor)->cellar->user->email;
            Mail::to($userEmail)->send(new ValidationError($e->errors()));
    
            throw $e;
        }
        catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    
    


}
