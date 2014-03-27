<?php

namespace Mozcu\MozcuBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Mozcu\MozcuBundle\Entity\User;
use Mozcu\MozcuBundle\Entity\Profile;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserService extends BaseService{
    
    /**
     *
     * @var EncoderFactory
     */
    private $encoder_factory;
    
    /**
     *
     * @var SecurityContext
     */
    protected $securityContext;
    
    public function __construct(EntityManager $entityManager, EncoderFactory $encoderFactory, SecurityContext $securityContext) {
        $this->encoder_factory = $encoderFactory;
        $this->securityContext = $securityContext;
        parent::__construct($entityManager);
    }
    
    /**
     * 
     * @return string
     */
    public function toString() {
        return 'UserService';
    }
    
    /**
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @return User
     */
    public function createUser($username, $password, $email) {
        $factory = $this->encoder_factory;
        $group = $this->getEntityManager()->getRepository('Mozcu\MozcuBundle\Entity\Group')->find(2);
        $user = new User();
        
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($password, $user->getSalt());
        
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->addGroup($group);
        
        $profile = new Profile;
        $profile->setPaypalEmail($email);
        $user->addProfile($profile);
        $profile->setUser($user);
        
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        
        return $user;
    }
    
    public function updateUser(User $user, $email, $password) {
        $factory = $this->encoder_factory;
        
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($password, $user->getSalt());
        
        $user->setEmail($email);
        $user->setPassword($password);
        
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        
        return $user;
    }
    
    /**
     * 
     * @param string $user
     * @param string $email
     * @return array
     */
    public function checkUserDisponibility($username, $email) {
        $repo = $this->getEntityManager()->getRepository('MozcuMozcuBundle:User');
        
        $user = $repo->findOneBy(array('username' => $username));
        if($user) {
            return array('available' => false, 'message' => "Nombre de usuario $username en uso");
        }
        
        $user = $repo->findOneBy(array('email' => $email));
        if($user) {
            return array('available' => false, 'message' => "Email $email en uso");
        }
        
        return array('available' => true);
    }
    
    /**
     * 
     * @param \Entities\User $user
     */
    public function logUser(User $user) {
        /* 'secured_area' is the name of the firewall */
        $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
        $this->securityContext->setToken($token);
    }

}