<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cantina extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Cantinas';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_cantina';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'città',
        'provincia',
        'via',
        'n°_civico',
        'dimensione Mq',
        'numero_sensori',
    ];
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'Ass_cantinas', 'id_cantina', 'id_user');
    }

}
