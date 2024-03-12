<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'Users';

    protected $primaryKey = 'id_user';


    protected $fillable = [
        'nome',
        'cognome',
        'email',
        'foto',
        'password',
    ];
    protected $hidden = [
        'password',
    ];

    public function cellars()
    {
        return $this->belongsToMany('App\Models\Cellar', 'ass_cellars', 'id_user', 'id_cellar');
    }
}
