<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Entity\AlbumImageRepository")
 */
class AlbumImage extends Image {
    
    /**
     * @ORM\OneToOne(targetEntity="Album", inversedBy="image")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     **/
    protected $album;
    
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
     * @return AlbumImage
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
     * @return AlbumImage
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
     * @return AlbumImage
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
     * Set album
     *
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return AlbumImage
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

    /**
     * Add presentations
     *
     * @param \Mozcu\MozcuBundle\Entity\ImagePresentation $presentations
     * @return AlbumImage
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
     * @var string
     */
    protected $temporal_file_name;


    /**
     * Set temporal_file_name
     *
     * @param string $temporalFileName
     * @return AlbumImage
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