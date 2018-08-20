<?php

namespace AppBundle\Service;


class NotificationMail
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail()
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('New comment added')
            ->setFrom('classified@ads.com')
            ->setTo('user@gmail.com')
            ->setCharset('UTF-8')
            ->setContentType('text/html')
            ->setBody('You advert has received a comment');

        $this->mailer->send($message);
    }
}