<?php
namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        // 命令的名字（"bin/console" 后面的部分）
        ->setName('test')

        // the short description shown while running "php bin/console list"
        // 运行 "php bin/console list" 时的简短描述
        ->setDescription('test console command.')

        // the full command description shown when running the command with
        // the "--help" option
        // 运行命令时使用 "--help" 选项时的完整命令描述
        ->setHelp("This command only a test ....");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}