<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    use HasFactory;

    protected $table = 'Fotos';
    protected $primaryKey = 'id_foto';

    protected $fillable = [
        'id_sensor',
        'path'
    ];

    public function sensore()
    {
        return $this->belongsTo('App\Models\Sensor', 'id_sensor', 'id_sensor');
    }
}
