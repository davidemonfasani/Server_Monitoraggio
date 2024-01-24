<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensore extends Model
{
    use HasFactory;

    protected $table = 'Sensores';
    protected $primaryKey = 'id_sensore';
    protected $fillable = [
        'id_cantina'
    ];
    public function cantina()
    {
        return $this->belongsTo('App\Models\Cantina', 'id_cantina', 'id_cantina');
    }
    public function keepAlives()
    {
        return $this->hasMany('App\Models\KeepAlive', 'id_sensore', 'id_sensore');
    }
    public function monitoraggios()
    {
        return $this->hasMany('App\Models\Monitoraggio', 'id_sensore', 'id_sensore');
    }   
    public function fotos()
    {
        return $this->hasMany('App\Models\Foto', 'id_sensore', 'id_sensore');
    }

}
