<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Entity\UserRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface, \Serializable {
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     *
     */
    private $groups;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\OneToMany(targetEntity="Profile", mappedBy="user", cascade={"persist"})
     **/
    private $profiles;
    
    /**
     * @ORM\Column(name="old_password", type="string", length=255, nullable=true)
     */
    private $oldPassword;
    
    /**
     * @ORM\Column(name="old_login", type="boolean")
     */
    private $oldLogin;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->groups = new ArrayCollection();
        $this->profiles = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->oldLogin = false;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return $this->groups->toArray();
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add groups
     *
     * @param \Mozcu\MozcuBundle\Entity\Group $groups
     * @return User
     */
    public function addGroup(\Mozcu\MozcuBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;
    
        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Mozcu\MozcuBundle\Entity\Group $groups
     */
    public function removeGroup(\Mozcu\MozcuBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add profiles
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $profiles
     * @return User
     */
    public function addProfile(\Mozcu\MozcuBundle\Entity\Profile $profiles)
    {
        $this->profiles[] = $profiles;
    
        return $this;
    }

    /**
     * Remove profiles
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $profiles
     */
    public function removeProfile(\Mozcu\MozcuBundle\Entity\Profile $profiles)
    {
        $this->profiles->removeElement($profiles);
    }

    /**
     * Get profiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * 
     * @return Profile
     */
    public function getCurrentProfile() {
        return $this->profiles->first();
    }
    
    /**
     * 
     * @return string
     */
    public function getCurrentName() {
        if(is_null($this->getCurrentProfile()->getName())) {
            return $this->username;
        } else {
            return $this->getCurrentProfile()->getName();
        }
    }

    /**
     * Set oldPassword
     *
     * @param string $oldPassword
     * @return User
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    
        return $this;
    }

    /**
     * Get oldPassword
     *
     * @return string 
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * Set oldLogin
     *
     * @param boolean $oldLogin
     * @return User
     */
    public function setOldLogin($oldLogin)
    {
        $this->oldLogin = $oldLogin;
    
        return $this;
    }

    /**
     * Get oldLogin
     *
     * @return boolean 
     */
    public function getOldLogin()
    {
        return $this->oldLogin;
    }
    
    /**
     * 
     * @return Profile
     */
    public function getProfile() {
        return $this->profiles->first();
    }
}