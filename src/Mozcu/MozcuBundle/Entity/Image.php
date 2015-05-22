<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Repository\ImageRepository")
 * @ORM\Table(name="image")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({1 = "AlbumImage", 2 = "ProfileImage"})
 */
abstract class Image {
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $main;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $mimeType;
    
    /**
     * @ORM\OneToMany(targetEntity="ImagePresentation", mappedBy="image", cascade={"persist", "remove"})
     **/
    protected $presentations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $temporal_file_name;
    
    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->main = false;
        $this->presentations = new ArrayCollection();
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
     * @return Image
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
     * @return Image
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
     * @return Image
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
     * @return Image
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
     * Set temporal_file_name
     *
     * @param string $temporalFileName
     * @return Image
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