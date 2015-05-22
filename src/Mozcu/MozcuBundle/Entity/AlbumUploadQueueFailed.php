<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Repository\AlbumUploadQueueFailedRepository")
 * @ORM\Table(name="album_upload_queue_failed")
 */
class AlbumUploadQueueFailed {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\Column(name="success", type="boolean")
     */
    private $success;
    
    /**
     * @ORM\Column(name="attempts", type="integer")
     */
    private $attempts;

    /**
     * @ORM\OneToOne(targetEntity="Album")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     */
    private $album;
    
    /**
     * @ORM\Column(name="in_process", type="boolean")
     */
    private $inProcess;
    
    /**
     * @ORM\Column(name="to_update", type="boolean")
     */
    private $toUpdate;
    
    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->success = false;
        $this->attempts = 0;
        $this->inProcess = false;
        $this->toUpdate =  false;
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
     * @return AlbumUploadQueueFailed
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
     * Set success
     *
     * @param boolean $success
     * @return AlbumUploadQueueFailed
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    
        return $this;
    }

    /**
     * Get success
     *
     * @return boolean 
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Set attempts
     *
     * @param integer $attempts
     * @return AlbumUploadQueueFailed
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;
    
        return $this;
    }

    /**
     * Get attempts
     *
     * @return integer 
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * Set album
     *
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return AlbumUploadQueueFailed
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
     * Set inProcess
     *
     * @param boolean $inProcess
     * @return AlbumUploadQueueFailed
     */
    public function setInProcess($inProcess)
    {
        $this->inProcess = $inProcess;
    
        return $this;
    }

    /**
     * Get inProcess
     *
     * @return boolean 
     */
    public function getInProcess()
    {
        return $this->inProcess;
    }

    /**
     * Set toUpdate
     *
     * @param boolean $toUpdate
     * @return AlbumUploadQueueFailed
     */
    public function setToUpdate($toUpdate)
    {
        $this->toUpdate = $toUpdate;
    
        return $this;
    }

    /**
     * Get toUpdate
     *
     * @return boolean 
     */
    public function getToUpdate()
    {
        return $this->toUpdate;
    }
}