<?php


use App\Controller\Auth\AuthController;
use App\Controller\Controller;
use Phroute\Phroute\RouteCollector;
use App\Controller\Actas\ActasController;

$router = Config\Providers\RouteServiceProviders::getInstance()->getRouter();

    $router->get('/', [Controller::class, 'hello']);

    $router->group(['prefix' => 'api'], function (RouteCollector $router) {

      $router->post('/login', [AuthController::class, 'Login']);
      $router->post('/signout', [AuthController::class, 'SignOut']);


      $router->group(['before' => 'auth'], function(RouteCollector $router){

          $router->get('/actas', [ActasController::class, 'index']);
          $router->post('/actas', [ActasController::class, 'store']);
          $router->get('/actas/{id}', [ActasController::class, 'show']);
          $router->put('/actas/{id}', [ActasController::class, 'update']);
          $router->delete('/actas/{id}', [ActasController::class, 'destroy']);
          $router->post('/actas/attach-asistentes', [ActasController::class, 'attachAsistentes']);
          $router->post('/actas/make-compromisos', [ActasController::class, 'makeCompromisos']);

          $router->get('find-by-id-or-asunto-actas', [ActasController::class, 'findByIdOrAsuntoActas']);
          $router->get('actas-by-date', [ActasController::class, 'actasByDate']);

          $router->get('/usuarios', [Controller::class, 'getUsers']);

          $router->get('/posts', function () {
              echo 'posts';
          });

     });
});

