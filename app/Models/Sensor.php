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
        'id_cellar',
        'TemperaturaMax',//target che deve mantenere
        'UmiditàMax',
        'TemperaturaMin',//target che deve mantenere
        'UmiditàMin',
        'TemperaturaNow',
        'UmiditàNow',
        'PesoNow'
    ];
    public function cellar()
    {
        return $this->belongsTo('App\Models\cellar', 'id_cellar', 'id_cellar');
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
