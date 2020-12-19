<?php


namespace sf\web;

use sf\view\Compiler;

class Controller
{
    public function render($view, $params = [])
    {
        (new Compiler())->compile($view, $params);
    }
}
