<?php

namespace app\services\interfaces;

interface IRenderService {
    public function renderTmpl(string $template, array $params = []);
}