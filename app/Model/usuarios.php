<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class usuarios extends Model
{
    protected $table = 'usuarios';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombres',
        'apellidos',
        'username',
        'password',
        'tipo_id'
    ];
    const USER_TYPE = [
        'ADMIN' => 1,
        'USER' => 0
    ];
    protected $hidden = [
        'password'
    ];


    public function actas(){
        return $this->hasMany(actas::class,'id_usuario');
    }
}