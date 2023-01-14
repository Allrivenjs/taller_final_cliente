<?php
namespace App\Middleware;

use JetBrains\PhpStorm\NoReturn;
use Phroute\Phroute\RouteCollector;

class Middleware
{
    protected static array $middleware = [];
    private static RouteCollector $router;
    public function __construct(RouteCollector $router)
    {
        self::$router = $router;
    }

    #[NoReturn] public function addMiddleware($middleware, string $method): void
    {
        self::$middleware[$method] = $middleware;
        self::registerMiddleware();
    }
    public static function getMiddleware(): array
    {
       return self::$middleware;
    }

    /**
     * @return void
     */
    public static function registerMiddleware(): void
    {
        foreach (self::getMiddleware() as $name => $middleware) {
            self::$router->filter($name, function () use ($middleware) {
               return ($middleware)->handle();
            });
        }
    }
}