<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AssCellar;
use App\Models\cellar;


class CellarController extends Controller
{
    public function fetch_cellars(Request $request){
        try {

            $request->validate([
                'id_user' => [
                    'required',
                    'integer',
                ]
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
