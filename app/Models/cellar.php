<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cellar extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Cellars';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_cellar';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'citta',
        'provincia',
        'via',
        'n_civico',
        'dimensione Mq',
        'numero_sensori',
    ];
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'ass_cellars', 'id_cellar', 'id_user');
    }
    public function sensors()
    {
        return $this->hasMany('App\Models\Sensor', 'id_cellar', 'id_cellar');
    }

}
