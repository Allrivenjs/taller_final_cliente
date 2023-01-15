<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class actas extends Model
{
    protected $table = 'actas';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'asunto',
        'fecha_creacion',
        'hora_inicio',
        'hora_final',
        'responsable_id',
        'descripcion_hechos',
        'orden_del_dia',
        'creador_id'
    ];

    public function responsable(){
        return $this->belongsTo(usuarios::class, 'responsable_id');
    }

    public function creador(){
        return $this->belongsTo(usuarios::class, 'creador_id');
    }

}