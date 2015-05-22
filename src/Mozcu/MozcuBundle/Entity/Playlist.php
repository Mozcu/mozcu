<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Repository\PlaylistRepository")
 * @ORM\Table(name="playlist")
 */
class Playlist {
    
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
     * @ORM\Column(type="array", nullable=true)
     */
    private $extraData;
    
    /**
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="playlists")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     **/
    private $profile;
    
    /**
     * @ORM\ManyToMany(targetEntity="Song", mappedBy="playlists")
     */
    private $songs;
    
    public function __construct() {
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
     * @return Playlist
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
     * Set extraData
     *
     * @param array $extraData
     * @return Playlist
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;
    
        return $this;
    }

    /**
     * Get extraData
     *
     * @return array 
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * Set profile
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return Playlist
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
     * Add songs
     *
     * @param \Mozcu\MozcuBundle\Entity\Song $songs
     * @return Playlist
     */
    public function addSong(\Mozcu\MozcuBundle\Entity\Song $songs)
    {
        $this->songs[] = $songs;
    
        return $this;
    }

    /**
     * Remove songs
     *
     * @param \Mozcu\MozcuBundle\Entity\Song $songs
     */
    public function removeSong(\Mozcu\MozcuBundle\Entity\Song $songs)
    {
        $this->songs->removeElement($songs);
    }

    /**
     * Get songs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSongs()
    {
        return $this->songs;
    }
}