<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class LaunchConsumerCommand extends ContainerAwareCommand
{
    const CONSUMER_LAUNCH_COMMAND = 'rabbitmq:consumer';

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('app:consumer:launch')
            ->addArgument('consumer', InputArgument::REQUIRED, 'Consumer name')
            ->addArgument('number', InputArgument::REQUIRED, 'Consumer number')
            ->setHelp('Launch x consumers');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $number = $input->getArgument('number');
        $consumer = $input->getArgument('consumer');

        $binPath = realpath(sprintf('%s/bin/console', $this->getContainer()->getParameter('kernel.project_dir')));

        for ($i = 0; $i < $number; ++$i) {
            $process = new Process(sprintf('%s %s %s', $binPath, self::CONSUMER_LAUNCH_COMMAND, $consumer));
            $process->start();
        }

        $output->writeln(sprintf('<info>%d %s consumer(s) launched</info>', $number, $consumer));
    }
}
