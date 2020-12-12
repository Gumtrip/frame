<?php


namespace sf\web;


class Controller
{
    public function render($view,$params = []){


        extract($params);

        return require '../resources/views/' . $view . '.php';
    }
}