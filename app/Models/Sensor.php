<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $table = 'Sensors';
    protected $primaryKey = 'id_sensor';
    protected $fillable = [
        'id_cellar',
        'TemperaturaMax',//target che deve mantenere
        'UmiditaMax',
        'TemperaturaMin',//target che deve mantenere
        'UmiditaMin',
        'TemperaturaNow',
        'UmiditaNow',
        'PesoNow'
    ];
    public function cellar()
    {
        return $this->belongsTo('App\Models\cellar', 'id_cellar', 'id_cellar');
    }
    public function keepAlives()
    {
        return $this->hasMany('App\Models\KeepAlive', 'id_sensor', 'id_sensor');
    }
    public function monitoraggios()
    {
        return $this->hasMany('App\Models\Monitoraggio', 'id_sensor', 'id_sensor');
    }
    public function fotos()
    {
        return $this->hasMany('App\Models\Foto', 'id_sensor', 'id_sensor');
    }

}
