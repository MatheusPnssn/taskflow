<?php

namespace App\Router;
class Router{
    public $route, $controller, $function;

    public static array $instances;
    public ?string $routeName = null;
    public function __construct($route, $controller, $function){
        $this->route = $route;
        $this->controller = $controller;
        $this->function = $function;
    }

    public function name(string $name): self{
        $this->routeName = $name;

        return $this;
    }

    static function get($route, $controller, $function): Router{
        self::$instances[] = new self($route, $controller, $function);

        return new Router($route, $controller, $function);
    }

    static function post($route, $controller, $function): Router{
        self::$instances[] = new self($route, $controller, $function);

        return new Router($route, $controller, $function);
    }

    static function delete($route, $controller, $function): Router{
        self::$instances[] = new self($route, $controller, $function);

        return new Router($route, $controller, $function);
    }

    static function patch($route, $controller, $function): Router{
        self::$instances[] = new self($route, $controller, $function);

        return new Router($route, $controller, $function);
    }

    static function dispatch($currentURLInfo){

        $controllerName = null;
        $functionName = null;

        foreach (self::$instances as $instance){
            if (is_array($instance->route)){
                foreach ($instance->route as $route){
                    if ($route == $currentURLInfo['route']){
                        $functionName = $instance->function;
                        $controllerName = $instance->controller;
                        break;
                    }
                }
            } else if ($instance->route == $currentURLInfo['route']){
                $functionName = $instance->function;
                $controllerName = $instance->controller;
            }
        }

        if ($controllerName == null || $functionName == null){
            die;
        }

        $controllerName::$functionName();
        exit;
    }
}