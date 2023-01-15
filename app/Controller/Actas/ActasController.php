<?php

namespace App\Controller\Actas;

use App\Controller\Controller;
use App\Model\actas;
use App\Model\usuarios;
use Sirius\Validation\Validator;


class ActasController extends Controller
{
    public function index(): ActasController
    {
        return $this->response(actas::query()->with(['responsable', 'creador'])->get());
    }
    //generar funciones para una api rest
    public function store(): ActasController
    {
        $validator = new Validator();
        $validator->add('asunto', 'required');
        $validator->add('fecha_creacion', 'required');
        $validator->add('hora_inicio', 'required');
        $validator->add('hora_final', 'required');
        $validator->add('responsable_id', 'required');
        $validator->add('creador_id', 'required');
        $validator->add('descripcion_hechos', 'required');

        if(!$validator->validate($this->request->all())){
            $validator->addMessage('asunto', 'El asunto es requerido');
            $validator->addMessage('fecha_creacion', 'La fecha_creacion es requerida');
            $validator->addMessage('hora_inicio', 'La hora_inicio es requerida');
            $validator->addMessage('hora_final', 'La hora_final es requerida');
            $validator->addMessage('responsable_id', 'La responsable_id es requerida');
            $validator->addMessage('creador_id', 'El creador_id es requerido');
            $validator->addMessage('descripcion_hechos', 'El descripcion_hechos es requerido');
            $validator->addMessage('orden_del_dia', 'El orden_del_dia es requerido');
            return $this->response([
                'message' => 'Error en los datos',
                'errors' => $validator->getMessages()
            ], 400);
        }
        $validate = $this->request->only(['asunto', 'orden_del_dia', 'fecha_creacion', 'hora_inicio', 'hora_final', 'responsable_id', 'creador_id', 'descripcion_hechos']);
        $usuario = usuarios::query()->find($validate['creador_id']);
        if(is_null($usuario)) return $this->response([
            'message' => 'El creador_id no existe'
        ], 400);
        $responsable = usuarios::query()->find($validate['responsable_id']);
        if(is_null($responsable)) return $this->response([
            'message' => 'El responsable_id no existe'
        ], 400);
        $acta = actas::query()->create($validate);
        return $this->response([
            'message' => 'Acta creada',
            'acta' => $acta
        ]);
    }
    public function show($id): ActasController
    {
        return $this->response(actas::query()->find($id));
    }
    public function update($id): ActasController
    {
        $actas = actas::query()->find($id);
        $actas->fill($this->request->all());
        $actas->save();
        return $this->response($actas);
    }
    public function destroy($id): ActasController
    {
        $actas = actas::query()->find($id);
        $actas->delete();
        return $this->response($actas);
    }
}