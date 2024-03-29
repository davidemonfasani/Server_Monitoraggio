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
        'id_sensor',
        'Temperatura',
        'Umidita',
        'Peso'
    ];
}
