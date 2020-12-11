<?php
namespace App\Controllers;

class IndexController
{
    public function index(){
        $name = 'Tom';
        // 注意，使用 composer 之后， 他默认都会在app 目录寻找 php 文件！
        require '../resources/views/index.php';
    }
}