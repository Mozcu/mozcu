<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Repository\ProfileLinkRepository")
 * @ORM\Table(name="profile_link")
 */
class ProfileLink {
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;
    
    /**
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="links")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     **/
    private $profile;
    

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
     * Set name
     *
     * @param string $name
     * @return ProfileLink
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return ProfileLink
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set profile
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return ProfileLink
     */
    public function setProfile(\Mozcu\MozcuBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;
    
        return $this;
    }

    /**
     * Get profile
     *
     * @return \Mozcu\MozcuBundle\Entity\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }
}