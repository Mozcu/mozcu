<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_email_sent")
 */
class UserEmailSent
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;
    
    /**
     * @ORM\Column(name="last_user_id", type="integer")
     */
    private $lastUserId;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set type
     *
     * @param string $type
     * @return UserEmailSent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set lastUserId
     *
     * @param integer $lastUserId
     * @return UserEmailSent
     */
    public function setLastUserId($lastUserId)
    {
        $this->lastUserId = $lastUserId;

        return $this;
    }

    /**
     * Get lastUserId
     *
     * @return integer 
     */
    public function getLastUserId()
    {
        return $this->lastUserId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserEmailSent
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
}
