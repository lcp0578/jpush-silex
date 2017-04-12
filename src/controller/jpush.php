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
use JPush\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * @var $controller \Silex\ControllerCollection
 */
$controller = $app['controllers_factory'];
$controller->get('/', function(){
    return 'jpush home page';
});
$controller->get('/go', function(Request $request){
    $type = $request->get('type', null);
    $registrationId = $request->get('registration_id', null);
    $userId = $request->get('user_id', null);
    if (!in_array($type, ['all', 'android', 'ios', 'winphone'])) 
        return new JsonResponse(['type error']);
    if('ios' == $type){
        $config = [
            'appKey' => 'fde3f09baef4b9001c32b290',
            'masterSecret' => 'fdf0c1121d752c05dd7667ce'
        ];
    }else{
        $config = [
            'appKey' => '0f2e2989f659e3a6d84b68a9',
            'masterSecret' => 'e4052cfa599fc7b21db681e9'
        ];
    }
    // jpush sdk
    $client = new Client($config['appKey'], $config['masterSecret']);
    $pusher = $client->push();
    $pusher->setPlatform('all'); // 'all', 'android', 'ios', 'winphone'
    //$pusher->addAllAudience();
    $pusher->addRegistrationId($registrationId);
    $pusher->setNotificationAlert('Hello, JPush, user_id:' . $userId);
    try {
        $pusher->send();
    } catch (\JPush\Exceptions\JPushException $e) {
        // try something else here
        file_put_contents('push.log', '[' . date('Y-m-d H:i:s') . ']' . json_encode($e) . "\r\n", FILE_APPEND);
    }
});
return $controller;