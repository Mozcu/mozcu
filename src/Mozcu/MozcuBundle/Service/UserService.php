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
    
    public function updateUser(User $user, $username, $email, $password = null, $flush = true) {
        $factory = $this->encoder_factory;
        
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($password, $user->getSalt());
        
        $user->setUsername($username);
        $user->setEmail($email);
        
        if(!is_null($password)) {
            $this->changePassword($user, $password, false);
        }
        
        $this->getEntityManager()->persist($user);
        if($flush) {
            $this->getEntityManager()->flush();
        }
        
        return $user;
    }
    
    /**
     * 
     * @param string $username
     * @return boolean
     */
    public function checkUsernameDisponibility($username) {
        $repo = $this->getEntityManager()->getRepository('MozcuMozcuBundle:User');
        
        $user = $repo->findOneBy(array('username' => $username));
        if($user) {
            return false;
        }
        return true;
    }
    
    /**
     * 
     * @param string $email
     * @return boolean
     */
    public function checkEmailDisponibility($email) {
        $repo = $this->getEntityManager()->getRepository('MozcuMozcuBundle:User');
        
        $user = $repo->findOneBy(array('email' => $email));
        if($user) {
            return false;
        }
        return true;
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
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\User $user
     * @param string $password
     * @return boolean
     */
    public function oldLoginCheck(User $user, $password) {
        $storedPass = $user->getOldPassword();
        if(empty($storedPass) || $storedPass != (md5($password))) {
            return false;
        }
        $this->logUser($user);
        return true;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\User $user
     * @param string $password
     * @param boolean $flush
     * @return \Mozcu\MozcuBundle\Entity\User
     */
    public function changePassword(User $user, $password, $flush = true) {
        $factory = $this->encoder_factory;
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($password, $user->getSalt());
        
        $user->setPassword($password);
        if($user->getOldLogin()) {
            $user->setOldLogin(false);
        }
        
        $this->getEntityManager()->persist($user);
        if($flush) {
            $this->getEntityManager()->flush();
        }
        
        return $user;
    }

}