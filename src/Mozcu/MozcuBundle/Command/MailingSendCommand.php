<?php

namespace Mozcu\MozcuBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MailingSendCommand extends ContainerAwareCommand 
{
    protected function configure()
    {
        $this
            ->setName('mailing:send')
            ->setDescription('Envia un email a todos los usuarios')
            ->addArgument(
                'type',
                InputArgument::OPTIONAL,
                'Tipo de mail a enviar'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        
        // TODO
        
        $output->writeln('ok');
    }
}
