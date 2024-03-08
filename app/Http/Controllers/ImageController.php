<?php

namespace App\Http\Controllers;
use App\Models\Foto;
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
            'id_Sensor' => 'required|integer|exists:Sensors,id_Sensor',
        ]);

        $imageName = $request->id_Sensor.date('Y-m-d-H-i-s').'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        // Create a new record in the Fotos table
        $foto = new Foto();
        $foto->id_Sensor = $request->id_Sensor;
        $foto->path = 'images/Monitoring/'.$imageName;
        $foto->save();
        return response()->json(['inserimento' => 'immagine inserita e salvata con successo'], 201);
    } catch (\Exception $e) {
        Log::error('Exception');
        Log::error($e->getMessage());
        return response()->json(['error' => $e->getMessage()], 404);
    }
    }
}
