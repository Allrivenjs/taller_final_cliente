<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..');
$dotenv->load();
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$container = \DI\ContainerBuilder::buildDevContainer();

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();


getenv('APP_ENV') === 'development' ? error_reporting(E_ALL) : error_reporting(0);

/**
 * @return string
 */
function routeOfUser(): string
{
    $baseDir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    return 'http://' . $_SERVER['HTTP_HOST'] . $baseDir;
}
\Config\Cors\Cors::cors();

define('BASE_URL', routeOfUser());

$router = \Config\Providers\RouteServiceProviders::getInstance()->getRouter();
require_once __DIR__ . '/../config/RegisterMiddleware.php';
require_once __DIR__ . '/../routes/api.php';

/**
 * @return string
 */
function request_path(): string
{
    $request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $script_name = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
    $parts = array_diff_assoc($request_uri, $script_name);
    if (empty($parts))
    {
        return '/';
    }
    $path = implode('/', $parts);
    if (($position = strpos($path, '?')) !== FALSE)
    {
        $path = substr($path, 0, $position);
    }
    return $path;

}

try{
    $dispatcher = new Phroute\Phroute\Dispatcher($router->getData());
    $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'],request_path());
    echo $response;
}catch (\Phroute\Phroute\Exception\HttpException $e){
    $emitter = new \Zend\Diactoros\Response\SapiEmitter();
    $response = new Zend\Diactoros\Response\TextResponse($e->getMessage(), 400);
    $emitter->emit($response);
} catch (Error $e){
    $emitter = new \Zend\Diactoros\Response\SapiEmitter();
    $response = new Zend\Diactoros\Response\TextResponse($e->getMessage(), 500);
    $emitter->emit($response);
}
