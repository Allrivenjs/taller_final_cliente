<?php


use App\Controller\Auth\AuthController;
use App\Controller\Controller;
use Phroute\Phroute\RouteCollector;

$router = Config\Providers\RouteServiceProviders::getInstance()->getRouter();

    $router->get('/', [Controller::class, 'hello']);

    $router->group(['prefix' => 'api'], function (RouteCollector $router) {

      $router->post('/login', [AuthController::class, 'Login']);
      $router->post('/signout', [AuthController::class, 'SignOut']);


      $router->group(['before' => 'auth'], function(RouteCollector $router){

          $router->get('/actas', [App\Controller\Actas\ActasController::class, 'index']);
          $router->post('/actas', [App\Controller\Actas\ActasController::class, 'store']);
          $router->get('/actas/{id}', [App\Controller\Actas\ActasController::class, 'show']);
          $router->put('/actas/{id}', [App\Controller\Actas\ActasController::class, 'update']);
          $router->delete('/actas/{id}', [App\Controller\Actas\ActasController::class, 'destroy']);

          $router->get('/posts', function () {
              echo 'posts';
          });

     });
});

