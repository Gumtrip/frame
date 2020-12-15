<?php
define('SF_PATH', dirname(__DIR__));
require_once(SF_PATH . '/src/Sf.php');
require_once __DIR__ . '/../vendor/autoload.php';
$application = new sf\web\Application();
$application->run();
