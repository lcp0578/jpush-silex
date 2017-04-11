<?php
/**
 * filename
 *
 * @package: packname
 * @author: lcp0578@gmail.com
 * @date: 2017-04-11 PM11:37:21
 * @version: 0.0.1
 * @copyright: http://lcpeng.cn
 */
use Symfony\Component\HttpFoundation\Request;
/**
 * @var $controller \Silex\ControllerCollection
 */
$controller = $app['controllers_factory'];
$controller->get('/', function(){
    return 'jpush home page';
});
$controller->get('/go', function(Request $request){
    $type = $request->get('type', null);
    $appid = $request->get('appid', null);
    if('ios' == $type){
        $config = [
            
        ];
    }else{
        $config = [
        
        ];
    }
});
return $controller;