<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Repository\ProfileImageRepository")
 */
class ProfileImage extends Image {
    
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var boolean
     */
    protected $main;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $presentations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->presentations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ProfileImage
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
     * Set main
     *
     * @param boolean $main
     * @return ProfileImage
     */
    public function setMain($main)
    {
        $this->main = $main;
    
        return $this;
    }

    /**
     * Get main
     *
     * @return boolean 
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return ProfileImage
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    
        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Add presentations
     *
     * @param \Mozcu\MozcuBundle\Entity\ImagePresentation $presentations
     * @return ProfileImage
     */
    public function addPresentation(\Mozcu\MozcuBundle\Entity\ImagePresentation $presentations)
    {
        $this->presentations[] = $presentations;
    
        return $this;
    }

    /**
     * Remove presentations
     *
     * @param \Mozcu\MozcuBundle\Entity\ImagePresentation $presentations
     */
    public function removePresentation(\Mozcu\MozcuBundle\Entity\ImagePresentation $presentations)
    {
        $this->presentations->removeElement($presentations);
    }

    /**
     * Get presentations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPresentations()
    {
        return $this->presentations;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="images")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     **/
    private $profile;

    /**
     * Set profile
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return ProfileImage
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

    /**
     * @var string
     */
    protected $temporal_file_name;


    /**
     * Set temporal_file_name
     *
     * @param string $temporalFileName
     * @return ProfileImage
     */
    public function setTemporalFileName($temporalFileName)
    {
        $this->temporal_file_name = $temporalFileName;
    
        return $this;
    }

    /**
     * Get temporal_file_name
     *
     * @return string 
     */
    public function getTemporalFileName()
    {
        return $this->temporal_file_name;
    }
}