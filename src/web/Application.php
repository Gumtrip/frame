<?php
namespace sf\web;

use sf\base\Application as AbApp;

class Application extends AbApp
{

    public function handleRequest()
    {
        $router = $_GET['r'];
        list($controllerName, $actionName) = explode('/', $router);
        $ucController = ucfirst($controllerName);
        $controllerName = 'app\\Controllers\\' . $ucController . 'Controller';
        $controller = new $controllerName();
        return call_user_func([$controller,  ucfirst($actionName)]);
    }
}
