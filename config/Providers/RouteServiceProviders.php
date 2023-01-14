<?php
namespace Config\Providers;
use Phroute\Phroute\RouteCollector;
class RouteServiceProviders{
    private static RouteServiceProviders $instance;
    private RouteCollector $router;

    private function __construct()
    {
        if (!isset($this->router)) {
            $this->router = new RouteCollector();
        }
    }

    /**
     * @return RouteServiceProviders
     */
    public static function getInstance(): RouteServiceProviders
    {
        if(!isset(self::$instance)){
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @return RouteCollector
     */
    public function getRouter(): RouteCollector
    {
        return $this->router;
    }
}