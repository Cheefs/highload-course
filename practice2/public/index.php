<?php

include $_SERVER['DOCUMENT_ROOT'] .'/../common/Constants.php';
include AUTOLOAD;
include TRANSLATE;

use app\services\TwigRenderService;
use app\services\Request;
use app\services\Session;

$request = new Request();
$session = new Session([
    Session::USER_ID => 1, // Так как авторизация не реализована, а для работы корзины нужен пользоатель, по умолчанию зададим id
]);

$controller = $request->getControllerName()?: 'default';
$action = $request->getActionName()?: 'index';

$controllerClass = CONTROLLERS_PATH.ucfirst($controller).CONTROLLER;

if (class_exists($controllerClass)) {
    $controller = new $controllerClass(new TwigRenderService(), $request, $session);
    echo  $controller->run($action, $_GET['id']);
} else {
    header('Location: /');
}
