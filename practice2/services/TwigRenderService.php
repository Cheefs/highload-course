<?php

namespace app\services;

use app\services\interfaces\IRenderService;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Translate;

class TwigRenderService implements IRenderService {

    public function renderTmpl(string $template, array $params = []) {
        $loader = new FilesystemLoader(ROOT.'views');
        $twig = new Environment($loader);
        $params['translate'] = new Translate();
        return $twig->render($template.'.twig', $params);
    }
}