<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('send:emails')
            ->setDescription('Notify user about new comments')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows to notify user about new comments')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('New comments')
            ->setFrom('classified@ads.com')
            ->setTo('alternatywy2@gmail.com')
            ->setCharset('UTF-8')
            ->setContentType('text/html')
            ->setBody('Your ads received some comments');

        $this->getApplication()->getKernel()->getContainer()->get('mailer')->send($message);
        $output->writeln('Email sent successfuly');
    }
}