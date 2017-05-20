<?php

namespace Leinbg\LeinbgCli\Command;

use Leinbg\LeinbgCli\Exception\FileExistException;
use Leinbg\LeinbgCli\Utils\File;
use Symfony\Component\Console\Command\Command as Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class MakeLaravelCommand extends Command {

    protected $repoUrl = 'https://github.com/leinbg/laravelDemo/archive/master.zip';
    protected $repoName = 'laravelDemo-master';

    protected function configure()
    {
        $this->setName('make:laravel')
             ->setDescription('copy a customized laravel application.')
             ->addArgument('name', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Copying Laravel from github...</info>');
        $name = $input->getArgument('name');
        $directory = getcwd() . '/' . $name;

        // 1 download and extract application files
        $this->makeFiles($name, $directory);

        // 2. run commands process
        $this->runCommands($directory, $output);
        $output->writeln('<info>Laravel Application Ready!</info>');
    }

    public function makeFiles($appName, $appDirectory)
    {
        if (file_exists($appDirectory)) {
            throw new FileExistException("Project Directory '{$appName}' exists.");
        }
        (new File($this->repoUrl, $this->repoName, $appName))->make();
    }

    public function runCommands($appDirectory, $output)
    {
        $commands = $this->getCommands();
        $process = new Process($commands, $appDirectory);
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });
    }

    public function getCommands()
    {
        $commands = [
            'composer install',
            'composer run-script post-install-config-application',
        ];

        return implode(' && ', $commands);
    }
}
