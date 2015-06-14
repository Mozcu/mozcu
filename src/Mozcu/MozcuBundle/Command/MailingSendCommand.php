<?php

namespace Mozcu\MozcuBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Mozcu\MozcuBundle\Entity\UserEmailSent;

class MailingSendCommand extends ContainerAwareCommand 
{
    
    const CANT_PER_PAGE = 100;
    
    protected function configure()
    {
        $this
            ->setName('mailing:send')
            ->setDescription('Envia un email a todos los usuarios')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Tipo de mail a enviar'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        
        $sent = $this->getOrCreateSent($type);
        $users = $this->findUsers($sent);
        
        if(empty($users)) {
            $output->writeln('All emails sent');
            return 0;
        }
        
        $from = $this->getContainer()->getParameter('mail.noreply_address');
        $mailer = $this->getContainer()->get('mozcu_mozcu.email_service');
        $templating = $this->getContainer()->get('templating');
        $body = $templating->render('MozcuMozcuBundle:Emails:welcome.html.twig');
        $subject = 'Mozcu ha regresado!';
        
        foreach ($users as $user) {
            $output->writeln('Sending to [' . $user->getId() . '] ' . $user->getEmail());
            $mailer->send($from, $user->getEmail(), $subject, $body, 'text/html');
        }
        
        $this->updateSent($sent, $user->getId());
        
        $output->writeln('To be continued...');
        return 0;
    }
    
    /**
     * 
     * @param string $type
     * @return \Mozcu\MozcuBundle\Entity\UserEmailSent
     */
    private function getOrCreateSent($type)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $sent = $em->getRepository('MozcuMozcuBundle:UserEmailSent')->findOneBy(['type' => $type]);
        if (empty($sent)) {
            $sent = new UserEmailSent();
            $sent->setType($type)
                ->setLastUserId(0);
            $em->persist($sent);
            $em->flush();
        }
        
        return $sent;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\UserEmailSent $sent
     * @param int $lastUserId
     */
    private function updateSent(UserEmailSent $sent, $lastUserId)
    {
        $sent->setLastUserId($lastUserId);
        $this->getContainer()->get('doctrine.orm.entity_manager')->flush();
    }
    
    private function findUsers(UserEmailSent $sent) 
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();
        
        $q  = $qb->select('u')
            ->from('MozcuMozcuBundle:User', 'u')
            ->where('u.id > :userId')
            ->andWhere('u.isActive = 1')
            ->setParameters(['userId' => $sent->getLastUserId()])
            ->getQuery();
        
        $q->setFirstResult(0)
            ->setMaxResults(self::CANT_PER_PAGE);
        
        return $q->getResult();
    }
}
