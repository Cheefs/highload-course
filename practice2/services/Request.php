<?php

namespace app\services;


class Request {
    private $params;
    private $actionName;
    private $requestSting;
    private $controllerName;

    /**
     * @return mixed
     */
    public function getActionName() {
        return $this->actionName;
    }

    /**
     * @return mixed
     */
    public function getControllerName() {
        return $this->controllerName;
    }

    public function __construct() {
        $this->requestSting = $_SERVER['REQUEST_URI'];
        $this->parseRequest();
    }

    public function parseRequest() {
        $pattern = "#(?P<controller>\w+)[/]?(?P<action>\w+)?[/]?[?]?(?P<params>.*)#ui";
        if (preg_match_all($pattern, $this->requestSting, $matches)) {
            $this->controllerName = $matches['controller'][0];
            $this->actionName = $matches['action'][0];

            $this->params = [
              'get' => $_GET,
              'post' => $_POST
            ];
        }
    }

    public function get($key = null) {
        if ($key) {
            return $this->params['get'][$key]?? null;
        }
        return $this->params['get'];
    }

    public function post($key = null) {
        if ($key) {
            return $this->params['post'][$key]?? null;
        }
        return $this->params['post'];
    }
}