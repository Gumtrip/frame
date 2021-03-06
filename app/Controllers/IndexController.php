<?php
namespace App\Controllers;
use sf\web\Controller;
use app\Models\User;
use sf\Sf;

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
        $str = "{{ $name }}";

        $this->render('index',compact(['name','str']));
    }

    public function cacheIndex(){
        $cache = Sf::createObject('redis');
        $cache->set('test', '我就是测试一下缓存组件');
        $result = $cache->get('test');
//        $cache->flush();


        echo $result;
    }
}