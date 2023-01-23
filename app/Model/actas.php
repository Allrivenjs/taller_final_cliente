<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class actas extends Model
{
    protected $table = 'actas';
//    public $timestamps = false;
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

    public function responsable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(usuarios::class, 'responsable_id');
    }

    public function creador(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(usuarios::class, 'creador_id');
    }

    public function asistentes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(usuarios::class, 'asistentes', 'acta_id', 'asistente_id');
    }

    public function compromisos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(usuarios::class, 'compromisos', 'acta_id', 'responsable_id')
            ->withPivot('descripcion', 'fecha_inicio', 'fecha_final');
    }

}