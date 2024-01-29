<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssCantinas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Ass_cellars';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_Ass_cellar';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_user',
        'id_cellar',
    ];

    /**
     * Get the user that owns the AssCantina.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id_user');
    }

    /**
     * Get the cantina that owns the AssCantina.
     */
    public function cantina()
    {
        return $this->belongsTo('App\Models\Cellars', 'id_cellar', 'id_cellar');
    }
}