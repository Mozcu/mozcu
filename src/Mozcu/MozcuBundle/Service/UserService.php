<?php

namespace Mozcu\MozcuBundle\Service;

// Servicios
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validator;

// Entidades
use Mozcu\MozcuBundle\Entity\User;
use Mozcu\MozcuBundle\Entity\Profile;
use Mozcu\MozcuBundle\Entity\PasswordRecovery;

class UserService extends BaseService
{
    
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
    
    /**
     *
     * @var Validator
     */
    protected $validator;
    
    protected $recoveryPasswordTime;
    protected $invalidUsernames;
    
    public function __construct(
            EntityManager $entityManager, EncoderFactory $encoderFactory, SecurityContext $securityContext,
            Validator $validator, $recoveryPasswordTime, $invalidUsernames)
    {
        $this->encoder_factory = $encoderFactory;
        $this->securityContext = $securityContext;
        $this->validator = $validator;
        $this->recoveryPasswordTime = $recoveryPasswordTime;
        $this->invalidUsernames = $invalidUsernames;
        
        parent::__construct($entityManager);
    }
    
    /**
     * 
     * @return string
     */
    public function toString() 
    {
        return 'UserService';
    }
    
    /**
     * 
     * @param array $data
     * @return User
     */
    public function createUser(array $data) 
    {
        $factory = $this->encoder_factory;
        $group = $this->getEntityManager()->getRepository('Mozcu\MozcuBundle\Entity\Group')->find(2);
        $user = new User();
        
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($data['password'], $user->getSalt());
        
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($password);
        $user->addGroup($group);
        
        $profile = new Profile;
        $profile->setPaypalEmail($data['email']);
        $profile->setCity($data['city']);
        $country = $this->em->getRepository('MozcuMozcuBundle:Country')->find($data['country']);
        $profile->setCountry($country);
        
        $user->addProfile($profile);
        $profile->setUser($user);
        
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        
        return $user;
    }
    
    public function updateUser(User $user, $username, $email, $password = null, $flush = true) 
    {
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
    public function checkUsernameDisponibility($username) 
    {
        $repo = $this->getEntityManager()->getRepository('MozcuMozcuBundle:User');
        
        $user = $repo->findOneBy(array('username' => $username));
        if($user || in_array($username, $this->invalidUsernames)) {
            return false;
        }
        return true;
    }
    
    /**
     * 
     * @param string $email
     * @return boolean
     */
    public function checkEmailDisponibility($email) 
    {
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
    public function logUser(User $user) 
    {
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
    public function oldLoginCheck(User $user, $password) 
    {
        $storedPass = $user->getOldPassword();
        if(!$user->getOldLogin() || empty($storedPass) || $storedPass != (md5($password))) {
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
    public function changePassword(User $user, $password, $flush = true) 
    {
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
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @param array $accountData
     * @return array
     */
    public function validateAccountData(array $accountData) 
    {
        $response =  array('success' => false);
        
        if(!$this->validateEmail($accountData['email'])) {
            $response['message'] =  "El formato del email es invalido: {$accountData['email']}";
        }
        if(!$this->checkUsernameDisponibility($accountData['username'])) {
            $response['message'] = "El nombre {$accountData['username']} esta siendo utilizado";
        }
        if(!$this->checkEmailDisponibility($accountData['email'])) {
            $response['message'] = "Ya existe una cuenta con el email {$accountData['email']}";
        }
        
        if(!isset($response['message'])) {
            $response['success'] = true;
        }
        
        return $response;
    }
    
    private function validateEmail($email) 
    {
        $emailConstraint = new EmailConstraint();
        $errors = $this->validator->validateValue(
            $email,
            $emailConstraint 
        );
        
        if (count($errors) > 0) {
            return false;    
        }
        return true;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\User $user
     * @return \Mozcu\MozcuBundle\Entity\PasswordRecovery
     */
    public function createPasswordRecovery(User $user) 
    {
        $now = new \DateTime();
        $hash = md5($user->getId() . $now->getTimestamp());
        
        $pr = new PasswordRecovery();
        $pr->setUser($user)
            ->setHash($hash);
        
        $this->getEntityManager()->persist($pr);
        $this->getEntityManager()->flush();
        
        return $pr;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\PasswordRecovery $passwordRecovery
     * @return boolean
     */
    public function passwordRecoveryIsOld(PasswordRecovery $passwordRecovery)
    {
        $now = new \DateTime();
        $diff = $now->getTimestamp() - $passwordRecovery->getCreatedAt()->getTimestamp();
        
        return $diff > $this->recoveryPasswordTime;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\PasswordRecovery $passwordRecovery
     */
    public function removePasswordRecovery(PasswordRecovery $passwordRecovery)
    {
        $this->getEntityManager()->remove($passwordRecovery);
        $this->getEntityManager()->flush();
    }
}