<?php

namespace App\Controller\Actas;

use App\Controller\Controller;
use App\Model\actas;

class ActasController extends Controller
{
    public function index()
    {
        return $this->response(actas::query()->get());
    }
}