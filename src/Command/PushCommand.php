<?php
namespace Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JPush\Client;

class PushCommand extends \Knp\Command\Command
{
    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        // 命令的名字（"bin/console" 后面的部分）
        ->setName('jpush:start')
    
        // the short description shown while running "php bin/console list"
        // 运行 "php bin/console list" 时的简短描述
        ->setDescription('jpush start console command.')
    
        // the full command description shown when running the command with
        // the "--help" option
        // 运行命令时使用 "--help" 选项时的完整命令描述
        ->setHelp("This command start push ....")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApplication();
        /**
         * @var $connect \Doctrine\DBAL\Connection
         */
        $connect = $app['db'];
        $result = $connect->fetchAll("SELECT jp.type, jp.registration_id,me.itemid, me.title FROM cheng_jpush jp join cheng_message me ON me.touser = jp.username WHERE me.push = 0 ORDER BY jp.id DESC");
        //$count = $connect->executeQuery('SELECT count(*) count FROM cheng_jpush')->fetch();
        //$output->writeln([$count['count']]);
        file_put_contents('debug.log', json_encode($result) . "\r\n", FILE_APPEND);
        foreach ($result as $r) {
            $output->writeln([$r['registration_id']]);
            $output->writeln([$r['type'], $r['itemid']]);
            $response = $this->push($r['registration_id'], $r['type'], $r['title'],$r['itemid']);
            if(!empty($response)){
                $result = $connect->update('cheng_message', [
                    'push' => 1
                ], [
                    'itemid' => $r['itemid']
                ]);
                file_put_contents('update_debug.log', json_encode($result) . "\r\n", FILE_APPEND);
            }
            file_put_contents('response_debug.log', json_encode($response) . "\r\n", FILE_APPEND);
        }
//         $output->writeln($dbal);
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        // 输出多行到控制台（在每一行的末尾添加 "\n"）
        $output->writeln([
            'hello',
            '============',
            '',
        ]);
    
        // outputs a message followed by a "\n"
        $output->writeln('Whoa!');
        
        // outputs a message without adding a "\n" at the end of the line
        $output->write('You are about to ');
        $output->write('create a user.');
    }
    /**
     * call jpush
     * @return \Command\JsonResponse
     */
    private function push($registrationId, $type, $title, $itemid)
    {
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
        if('ios' == $type){
            $pusher->iosNotification($title, [
                'extras' => [
                    'pushType' => '1',
                    'data' => "https://chengshi.91zhangyu.com/mobile/message.php?action=show&itemid=" . $itemid,
                    'notifyTitle' => $title,
                    'notifySubTitle' => '查看详情'
                ]
            ]);
        }else{
            $pusher->androidNotification($title, [
                'extras' => [
                    'pushType' => '1',
                    'data' => "https://chengshi.91zhangyu.com/mobile/message.php?action=show&itemid=" . $itemid,
                ]
            ]);
            
        }
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
            return $pusher->send();
        } catch (\JPush\Exceptions\JPushException $e) {
            // try something else here
            file_put_contents('push.log', '[' . date('Y-m-d H:i:s') . ']' . json_encode($e) . "\r\n", FILE_APPEND);
            return $e;
        }
    }
}