<?php


namespace sf;


class Sf
{
    public static function createObject($name)
    {
        $config = require(SF_PATH . "/config/$name.php");
        // create instance
        $instance = new $config['class']();
        unset($config['class']);
        // add attributes
        foreach ($config as $key => $value) {
            $instance->$key = $value;
        }
        return $instance;
    }
}