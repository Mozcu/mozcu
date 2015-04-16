<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Entity\SongRepository")
 * @ORM\Table(name="song", indexes={@ORM\Index(name="livesearch_idx", columns={"name"})})
 */
class Song {
    
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $trackNumber;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;
    
    /**
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="songs")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     **/
    private $album;
    
    /**
     * @ORM\ManyToMany(targetEntity="Playlist", inversedBy="songs")
     * @ORM\JoinTable(name="song_playlist")
     *
     */
    private $playlists;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $length;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $temporal_file_name;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $static_file_name;
    
    public function __construct() {
        $this->playlists = new ArrayCollection();
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
     * @return Song
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
     * Set trackNumber
     *
     * @param integer $trackNumber
     * @return Song
     */
    public function setTrackNumber($trackNumber)
    {
        $this->trackNumber = $trackNumber;
    
        return $this;
    }

    /**
     * Get trackNumber
     *
     * @return integer 
     */
    public function getTrackNumber()
    {
        return $this->trackNumber;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Song
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
     * Set album
     *
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return Song
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
     * Add playlists
     *
     * @param \Mozcu\MozcuBundle\Entity\Playlist $playlists
     * @return Song
     */
    public function addPlaylist(\Mozcu\MozcuBundle\Entity\Playlist $playlists)
    {
        $this->playlists[] = $playlists;
    
        return $this;
    }

    /**
     * Remove playlists
     *
     * @param \Mozcu\MozcuBundle\Entity\Playlist $playlists
     */
    public function removePlaylist(\Mozcu\MozcuBundle\Entity\Playlist $playlists)
    {
        $this->playlists->removeElement($playlists);
    }

    /**
     * Get playlists
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlaylists()
    {
        return $this->playlists;
    }

    /**
     * Set length
     *
     * @param string $length
     * @return Song
     */
    public function setLength($length)
    {
        $this->length = $length;
    
        return $this;
    }

    /**
     * Get length
     *
     * @return string 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set temporal_file_name
     *
     * @param string $temporalFileName
     * @return Song
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

    /**
     * Set static_file_name
     *
     * @param string $staticFileName
     * @return Song
     */
    public function setStaticFileName($staticFileName)
    {
        $this->static_file_name = $staticFileName;
    
        return $this;
    }

    /**
     * Get static_file_name
     *
     * @return string 
     */
    public function getStaticFileName()
    {
        return $this->static_file_name;
    }
}