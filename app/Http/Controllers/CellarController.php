<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AssCellar;
use App\Models\cellar;
use App\Traits\UserAuthTrait;

class CellarController extends Controller
{
    use UserAuthTrait;
    public function retrive_cellars(Request $request){
        try {

            $request->validate([
                'email' => 'required|string|email|max:55',
                'old_password' => 'required|string|max:300',
                'new_password' => 'required|string|max:300|different:old_password',
            ]);
            $cellars= cellar::Where('id_user' ,'=', $request->id_user)->get();
            $response=[
                "message"=> 'Login Avvenuto!',
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }


    }
    

}
