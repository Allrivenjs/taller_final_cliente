<?php


use App\Middleware\Middleware;

$router = Config\Providers\RouteServiceProviders::getInstance()->getRouter();
$middleware = new Middleware($router);
$middleware->addMiddleware(new \Traits\Middleware\AuthMiddlewareTrait(), 'auth');
