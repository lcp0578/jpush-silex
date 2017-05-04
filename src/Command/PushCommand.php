<?php
namespace Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $result = $connect->fetchAll("SELECT * FROM user");
        $count = $connect->executeQuery('SELECT count(*) count FROM user')->fetch();
        $output->writeln([$count['count']]);
        file_put_contents('debug.log', json_encode($result) . "\r\n", FILE_APPEND);
        foreach ($result as $r) {
            $output->writeln([$r['username']]);
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
}