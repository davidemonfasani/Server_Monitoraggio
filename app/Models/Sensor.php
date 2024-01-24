<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $table = 'Sensors';
    protected $primaryKey = 'id_Sensor';
    protected $fillable = [
        'id_cantina'
    ];
    public function cantina()
    {
        return $this->belongsTo('App\Models\Cantina', 'id_cantina', 'id_cantina');
    }
    public function keepAlives()
    {
        return $this->hasMany('App\Models\KeepAlive', 'id_Sensor', 'id_Sensor');
    }
    public function monitoraggios()
    {
        return $this->hasMany('App\Models\Monitoraggio', 'id_Sensor', 'id_Sensor');
    }
    public function fotos()
    {
        return $this->hasMany('App\Models\Foto', 'id_Sensor', 'id_Sensor');
    }

}
