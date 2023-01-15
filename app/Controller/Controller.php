<?php

namespace App\Controller;

use Config\Providers\Response;

class Controller extends Response
{
    public final function response($content = ''): static
    {
        (new Response())->responseContent($content)->send();
        return $this;
    }


    public function hello(): Response
    {
        return $this->response([
            'message' => 'Hello World',
        ]);
    }
}