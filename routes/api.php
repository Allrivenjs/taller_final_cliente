<?php


use App\Model\Controller\Auth\AuthController;
use App\Model\Controller\Email\SendEmailController;
use App\Model\Controller\Consolidado\consolidadoController;
use Phroute\Phroute\RouteCollector;
use App\Model\Controller\Credit\CreditController;
use App\Model\Controller\ExternalTransaction\ExternalTransactionController;


$router = Config\Providers\RouteServiceProviders::getInstance()->getRouter();

    $router->get('/', function () {
        echo 'Hello World!';
    });

    $router->group(['prefix' => 'api'], function (RouteCollector $router) {

      $router->post('/login', [AuthController::class, 'Login']);
      $router->post('/signout', [AuthController::class, 'SignOut']);


      $router->group(['before' => 'auth'], function(RouteCollector $router){

          $router->get('/posts', function () {
              echo 'posts';
          });

     });
});

