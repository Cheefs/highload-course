<?php

include $_SERVER['DOCUMENT_ROOT'] .'/../common/Constants.php';
include AUTOLOAD;
include TRANSLATE;

use app\services\TwigRenderService;
use app\services\Request;
use app\services\Session;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$timeStart = microtime();
$memoryUsageStart = memory_get_usage();

$request = new Request();
$session = new Session([
    Session::USER_ID => 1, // Так как авторизация не реализована, а для работы корзины нужен пользоатель, по умолчанию зададим id
]);
xdebug_start_trace();
$controller = $request->getControllerName()?: 'default';
$action = $request->getActionName()?: 'index';

$controllerClass = CONTROLLERS_PATH.ucfirst($controller).CONTROLLER;

if (class_exists($controllerClass)) {
    $controller = new $controllerClass(new TwigRenderService(), $request, $session);
    echo  $controller->run($action, $_GET['id']);
} else {
    header('Location: /');
}

$timeEnd = microtime();

$log = new Logger('app');
$log->pushHandler(new StreamHandler('log/app.log', Logger::DEBUG ));
$log->debug("app start",  [
    sprintf( "time spent: %s milliseconds", $timeStart - $timeEnd )
]);

$memoryUsageEnd = memory_get_usage();

$log->debug("memory usage",  [
    "before launch: $memoryUsageStart",
    "after: $memoryUsageEnd",
    sprintf('diff: %s', $memoryUsageEnd - $memoryUsageStart )
]);

xdebug_stop_trace();