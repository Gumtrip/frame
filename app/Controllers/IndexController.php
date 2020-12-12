<?php
namespace App\Controllers;
use sf\web\Controller;
use app\Models\User;

class IndexController extends Controller
{
    public function index(){
        $name = 'Tom';
        $user = User::findOne(['age' => 20, 'name' => 'harry']);
        $data = [
            'first' => 'awesome-php-zh_CN',
            'second' => 'simple-framework',
            'user' => $user
        ];
        var_dump($data);
        $this->render('index',compact(['name']));
    }
}