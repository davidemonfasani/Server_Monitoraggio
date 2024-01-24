<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoraggio extends Model
{
    use HasFactory;

    protected $table = 'Monitoraggios';
    protected $primaryKey = 'id_monitoraggio';
    protected $fillable = [
        'id_sensore',
        'Temperatura C°',
        'Umidità %',
        'peso Kg'
    ];
}
