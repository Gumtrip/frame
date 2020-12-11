<?php
require_once __DIR__ . '/../vendor/autoload.php';
$rootDir = dirname(__DIR__);
$router = $_GET['r'];
list($controllerName, $actionName) = explode('/', $router);
$ucController = ucfirst($controllerName);
$controllerName = 'app\\Controllers\\' . $ucController . 'Controller';
$controller = new $controllerName();
return call_user_func([$controller,  ucfirst($actionName)]);
