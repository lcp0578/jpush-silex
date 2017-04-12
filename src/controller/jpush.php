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
$controller->post('/go', function(Request $request){
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
        $pusher->options(array(
            // sendno: 表示推送序号，纯粹用来作为 API 调用标识，
            // API 返回时被原样返回，以方便 API 调用方匹配请求与返回
            // 这里设置为 100 仅作为示例
            // 'sendno' => 100,
            // time_to_live: 表示离线消息保留时长(秒)，
            // 推送当前用户不在线时，为该用户保留多长时间的离线消息，以便其上线时再次推送。
            // 默认 86400 （1 天），最长 10 天。设置为 0 表示不保留离线消息，只有推送当前在线的用户可以收到
            // 这里设置为 1 仅作为示例
            // 'time_to_live' => 1,
            // apns_production: 表示APNs是否生产环境，
            // True 表示推送生产环境，False 表示要推送开发环境；如果不指定则默认为推送开发环境
            'apns_production' => true,
            // big_push_duration: 表示定速推送时长(分钟)，又名缓慢推送，把原本尽可能快的推送速度，降低下来，
            // 给定的 n 分钟内，均匀地向这次推送的目标用户推送。最大值为1400.未设置则不是定速推送
            // 这里设置为 1 仅作为示例
            // 'big_push_duration' => 1
        ));
        return new JsonResponse($pusher->send());
    } catch (\JPush\Exceptions\JPushException $e) {
        // try something else here
        file_put_contents('push.log', '[' . date('Y-m-d H:i:s') . ']' . json_encode($e) . "\r\n", FILE_APPEND);
        return new JsonResponse($e);
    }
});
return $controller;