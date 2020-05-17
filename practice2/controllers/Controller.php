<?php

namespace app\controllers;

use app\helpers\Helper;
use app\services\interfaces\IRenderService;
use app\services\Request;
use app\services\Session;
use app\services\TwigRenderService;
use Translate;

/**
 * Базоый контроллер который будет хранить общее поведение для всех контроллеров
 * @property IRenderService $renderer
 * @property string $default
 * @property Request $request
 * @property Session $session
 *
*/
abstract class Controller {
    const ACTION = 'action';
    const CONTROLLER = 'Controller';
    protected $default = 'default';
    protected $renderer;
    protected $request;
    protected $session;

    public function __construct(TwigRenderService $renderer, Request $request, Session $session) {
        $this->renderer = $renderer;
        $this->request = $request;
        $this->session = $session;
    }

    public function run(string $action = null, int $id = null) {
        if ($action) {
            $action = self::ACTION.ucfirst($action);
            if (method_exists($this, $action)) {
                $this->beforeAction();
                return $this->$action($id);
            } else {
                return $this->notFound();
            }
        }
        return $this->notFound();
    }

    /**
     * @return mixed
     */
    public function getClassName() {
        return Helper::getClassName(get_called_class());
    }

    /**
     * @return string
     */
    public function getControllerName() {
        $className = $this->getClassName();
        $folder = str_replace(CONTROLLER,'', $className);
        return  strtolower($folder);
    }

    /**
     * @param string $view
     * @param array $params
     * @return false|string
     */
    protected function render(string $view, array $params = []) {
        $viewFolder = $this->getControllerName();
        return $this->renderTemplate(
            $viewFolder.DIRECTORY_SEPARATOR.$view,
            $params
        );
    }

    /**
     * @param string $template
     * @param array $params
     */
    protected function renderTemplate(string $template, array $params = []) {
       return $this->renderer->renderTmpl($template, $params);
    }

    /**
     * @param $action
     * @return true
     */
    public function redirect($action = null) {
        $controllerName = $this->getControllerName();
        $action = $action? '/'.$action : '';
        header("Location: /{$controllerName}{ $action }");
        return true;
    }

    /**
     * @return false|string
     */
    protected function notFound() {
      return $this->render('../common/error',[
          'message' => '404 Not Found',
          'translate' => new Translate()
      ]);
    }

    /**
     * Получение значения из массива GET
     * @param null $param
     * @return array|string|int|object|null
     */
    public function get($param = null) {
        return $this->request->get($param);
    }

    /**
     * Получение значения из массива POST
     * @param null $param
     * @return array|string|int|object|null
     */
    public function post($param = null) {
        return $this->request->post($param);
    }

    /**
     * Получение значения из сессии
     * @param null $param
     * @return array|string|int|object|null
     */
    public function session($param = null) {
        return $this->session->get($param);
    }

    /**
     * Если нам нужно будет выполнять чтото перед экшенами
     */
    protected function beforeAction() {
    }
}