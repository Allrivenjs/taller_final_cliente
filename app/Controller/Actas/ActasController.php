<?php

namespace App\Controller\Actas;

use App\Controller\Controller;
use App\Model\actas;
use App\Model\usuarios;
use Carbon\Carbon;
use Sirius\Validation\Validator;


class ActasController extends Controller
{
    public function index(): ActasController
    {
        return $this->response(actas::query()->with(['responsable', 'creador', 'asistentes', 'compromisos'])->get());
    }
    //generar funciones para una api rest
    public function store(): ActasController
    {
        $this->validation();
        $validate = $this->request->only(['asunto', 'orden_del_dia', 'fecha_creacion', 'hora_inicio', 'hora_final', 'responsable_id', 'creador_id', 'descripcion_hechos']);
        $usuario = usuarios::query()->find($validate['creador_id']);
        if(is_null($usuario)) return $this->response([
            'message' => 'El creador_id no existe'
        ], 400);
        $responsable = usuarios::query()->find($validate['responsable_id']);
        //obtener la hora de hora_fial
        $validate['hora_final'] = Carbon::make($validate['hora_final'])->format('H:i:s');
        $validate['hora_inicio'] = Carbon::make($validate['hora_inicio'])->format('H:i:s');
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
        return $this->response(actas::query()->find($id)->load(['responsable', 'creador', 'asistentes', 'compromisos']));
    }

    public function attachAsistentes(): ActasController
    {
        $validator = new Validator();
        $validator->add('acta_id', 'required');
        $validator->add('asistente_id', 'required');
        $validate = $this->request->only(['acta_id', 'asistente_id']);
        $acta = actas::query()->find($validate['acta_id']);
        if(is_null($acta)) return $this->response([
            'message' => 'El acta_id no existe'
        ], 400);
        $asistente = usuarios::query()->findMany($validate['asistente_id']);
        if(is_null($asistente)) return $this->response([
            'message' => 'El asistente_id no existe'
        ], 400);
        $acta->asistentes()->sync($validate['asistente_id']);
        return $this->response([
            'message' => 'Asistentes agregado',
            'acta' => $acta
        ]);
    }

    public function makeCompromisos(){
        $validator = new Validator();
        $validator->add('acta_id', 'required');
        $validator->add('datos', 'required');

        $validate = $this->request->only(['acta_id', 'datos']);
        $acta = actas::query()->find($validate['acta_id']);
        if(is_null($acta)) return $this->response([
            'message' => 'El acta_id no existe'
        ], 400);
        $responsable = usuarios::query()->findMany($validate['datos'][0]['responsable_id']);
        $validate['datos'][0]['fecha_inicio'] = Carbon::make($validate['datos'][0]['fecha_inicio']);
        $validate['datos'][0]['fecha_final'] = Carbon::make($validate['datos'][0]['fecha_final']);
        if(is_null($responsable)) return $this->response([
            'message' => 'El responsable_id no existe'
        ], 400);
        $acta->compromisos()->sync($validate['datos']);
        return $this->response([
            'message' => 'Compromisos agregado',
            'acta' => $acta
        ]);
    }

    public function update($id): ActasController
    {
        $acta = actas::query()->find($id);
        if(is_null($acta)) return $this->response([
            'message' => 'El acta no existe'
        ], 404);
        $this->validation();
        $validate = $this->request->only(['asunto', 'orden_del_dia', 'fecha_creacion', 'hora_inicio', 'hora_final', 'responsable_id', 'creador_id', 'descripcion_hechos']);
        $usuario = usuarios::query()->find($validate['creador_id']);
        $validate['hora_final'] = Carbon::make($validate['hora_final'])->format('H:i:s');
        $validate['hora_inicio'] = Carbon::make($validate['hora_inicio'])->format('H:i:s');
        if(is_null($usuario)) return $this->response([
            'message' => 'El creador_id no existe'
        ], 400);
        $responsable = usuarios::query()->find($validate['responsable_id']);
        if(is_null($responsable)) return $this->response([
            'message' => 'El responsable_id no existe'
        ], 400);
        $acta->update($validate);
        return $this->response([
            'message' => 'Acta actualizada',
            'acta' => $acta
        ]);
    }
    public function destroy($id): ActasController
    {
        $actas = actas::query()->find($id);
        if(is_null($actas)) return $this->response([
            'message' => 'El acta no existe'
        ], 404);
        $actas->delete();
        return $this->response(null);
    }

    protected function validation(): ActasController |null
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
        return null;
    }

    public function actasByDate(){
        $this->request->only(['fecha_inicio', 'fecha_final']);
        //validar que lleguen las fechas
        if (empty($this->request->get('fecha_inicio')) || empty($this->request->get('fecha_final'))) {
            return $this->response([
                'message' => 'Las fechas son requeridas'
            ], 400);
        }

        $fecha_inicio = Carbon::make($this->request->get('fecha_inicio'))->format('d-m-y');
        $fecha_final = Carbon::make($this->request->get('fecha_final'))->format('d-m-y');
        $actas = actas::query()->with(['responsable', 'creador'])->whereBetween('created_at', [$fecha_inicio, $fecha_final])->get();

        return $this->response([
            'message' => 'Actas por fecha',
            'actas' => $actas
        ]);
    }

    public function getCompromisosPendientes(){
        $actasCompromisos = actas::query()->with(['responsable','asistentes'])
            ->withWhereHas('compromisos', function ($query){
            $query->whereBetween('fecha_final', [Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(30)->format('Y-m-d')]);
        })->get();
        return $this->response([
            'message' => 'Compromisos pendientes',
            'actas' => $actasCompromisos
        ]);
    }
}