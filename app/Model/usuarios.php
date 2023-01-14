<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class usuarios extends Model
{
    protected $table = 'usuarios';
    protected $fillable = [
        'id',
        'nombre',
        'apellido',
        'username',
        'password',
        'tipo_id'
    ];


    public function actas(){

    }
}