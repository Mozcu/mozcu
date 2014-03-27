<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Entity\TagRepository")
 * @ORM\Table(name="tag")
 */
class Tag {
    
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
     * @ORM\ManyToMany(targetEntity="Album", mappedBy="tags")
     */
    private $albums;
    
    /**
     * @ORM\ManyToMany(targetEntity="Profile", mappedBy="tags")
     */
    private $profiles;
    
    public function __construct() {
        $this->profiles = new ArrayCollection();
        $this->songs = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Tag
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
     * Add profiles
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $profiles
     * @return Tag
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
     * Add albums
     *
     * @param \Mozcu\MozcuBundle\Entity\Album $albums
     * @return Tag
     */
    public function addAlbum(\Mozcu\MozcuBundle\Entity\Album $albums)
    {
        $this->albums[] = $albums;
    
        return $this;
    }

    /**
     * Remove albums
     *
     * @param \Mozcu\MozcuBundle\Entity\Album $albums
     */
    public function removeAlbum(\Mozcu\MozcuBundle\Entity\Album $albums)
    {
        $this->albums->removeElement($albums);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlbums()
    {
        return $this->albums;
    }
}