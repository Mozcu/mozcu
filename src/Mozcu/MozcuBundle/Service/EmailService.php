<?php

namespace Mozcu\MozcuBundle\Service;

// Services
use Swift_Mailer;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;

// Entities
use Mozcu\MozcuBundle\Entity\User;
use Mozcu\MozcuBundle\Entity\PasswordRecovery;

class EmailService 
{
    
    /**
     *
     * @var Swift_Mailer
     */
    private $mailer;
    
    /**
     *
     * @var TimedTwigEngine
     */
    private $templating;
    
    /**
     *
     * @var string
     */
    private $noReplyAddress;
    
    public function __construct($mailer, $templating, $noReplyAddress) 
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->noReplyAddress = $noReplyAddress;
    }
    
    /**
     * 
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return int
     */
    public function send($from, $to, $subject, $body, $bodyType = 'text/plain') 
    {
        $message = $this->mailer->createMessage()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body, $bodyType);
        return $this->mailer->send($message);
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\User $user
     * @param \Mozcu\MozcuBundle\Entity\PasswordRecovery $passwordRecovery
     * @return int
     */
    public function sendPasswordRecoveryEmail(User $user, PasswordRecovery $passwordRecovery) 
    {
        $body = $this->templating->render('MozcuMozcuBundle:Emails:passwordRecovery.html.twig', 
                                          array('passwordRecovery' => $passwordRecovery));
        $subject = 'Mozcu - Recuperar password';
        return $this->send($this->noReplyAddress, $user->getEmail(), $subject, $body, 'text/html');
    }
}
