<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeepAlive extends Model
{
    use HasFactory;

    protected $table = 'Keep_alives';
    protected $primaryKey = 'id_keep_alive';

    protected $fillable = [
        'id_sensore'
    ];
 
}
