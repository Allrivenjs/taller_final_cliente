<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class actas extends Model
{
    protected $table = 'actas';
    protected $fillable = [
        'id',
        'fecha',
        'hora',
        'lugar',
        'tipo',
        'descripcion',
        'usuario_id'
    ];

    public function usuarios(){
        return $this->belongsTo(usuarios::class);
    }
}