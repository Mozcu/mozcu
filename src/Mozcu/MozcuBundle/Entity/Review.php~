<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Entity\ReviewRepository")
 * @ORM\Table(name="review")
 */
class Review {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="reviews")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     **/
    private $profile;
    
    /**
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="reviews")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     **/
    private $album;
    
    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->isActive = true;
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
     * Set title
     *
     * @param string $title
     * @return Review
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Review
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Review
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return Review
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
     * Set profile
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return Review
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
     * Set album
     *
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return Review
     */
    public function setAlbum(\Mozcu\MozcuBundle\Entity\Album $album = null)
    {
        $this->album = $album;
    
        return $this;
    }

    /**
     * Get album
     *
     * @return \Mozcu\MozcuBundle\Entity\Album 
     */
    public function getAlbum()
    {
        return $this->album;
    }
}