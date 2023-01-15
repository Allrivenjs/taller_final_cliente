<?php

namespace App\Controller;

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

    public function getUserByToken(): usuarios | null
    {
        $token = $this->request->bearerToken();
        if (is_null($token)) {
            return null;
        }
        $user = JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'))->data;
        dd($user);
        return usuarios::query()->find($user->user_id);
    }

}