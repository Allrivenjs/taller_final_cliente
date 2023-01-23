<?php

namespace App\Controller;

use App\Model\actas;
use App\Model\usuarios;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Config\Providers\Response;

class Controller extends Response
{
    public Request $request;
    public usuarios | null $userAuth;

    public function __construct()
    {
        $this->request = Request::capture();
        $this->userAuth = $this->getUserByToken();
    }

    public final function response($content = '', $statusCode = 200): static
    {
        (new Response())->responseContent($content)
            ->setStatusCode($statusCode)->send();
        return $this;
    }


    public function hello(): Response
    {
        return $this->response([
            'message' => 'Hello World',
        ]);
    }

    public function getUserByToken(): \Illuminate\Database\Eloquent\Builder|array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
    {
        $token = $this->request->bearerToken();
        if (is_null($token)) {
            return null;
        }
        $user = JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'))->data;
        return usuarios::query()->find($user->user_id);
    }


    public function getUsers(): Response
    {
        return $this->response(usuarios::query()->get());
    }


    public function findByIdOrAsuntoActas(){
        $id = $this->request->get('id');
        $asunto = $this->request->get('asunto');
        if (is_null($id) && is_null($asunto)) {
            return $this->response([
                'message' => 'No se ha enviado el id o asunto'
            ], 400);
        }
//        query en actas si existe por id o por string de asunto
        $actas = actas::query()->when(!is_null($id), function ($query) use ($id) {
            return $query->where('id', $id);
        })->when(!is_null($asunto), function ($query) use ($asunto) {
            return $query->where('asunto', 'like', "%$asunto%");
        })->get();

        if (is_null($actas)) {
            return $this->response([
                'message' => 'No se ha encontrado el acta'
            ], 404);
        }
        return $this->response([
            'actas' => $actas
        ]);
    }


}