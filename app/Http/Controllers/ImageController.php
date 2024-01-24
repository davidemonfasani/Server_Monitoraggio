<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $imageName = $request->id_sensore.date('Y-m-d-H-i-s').'.'.$request->image->extension(); // l'id del sensore e il timestamp
            $request->image->move(public_path('images'), $imageName);//salva l'immagine nella cartella public/images con
            $response = array();
            $response['img inserita?'] = 'Immagine inserita';
                    
            $response['savattaggio database'] = 'Immagine inserita nel database';
                return response()->json($response, 201);

        } catch (\Exception $e) {
            Log::error('Exception');
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
