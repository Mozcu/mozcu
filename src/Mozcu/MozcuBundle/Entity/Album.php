<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Entity\AlbumRepository")
 * @ORM\Table(name="album", indexes={@ORM\Index(name="livesearch_idx", columns={"name", "artist_name"})})
 * @ORM\EntityListeners({ "Mozcu\MozcuBundle\Listener\AlbumListener" })
 */
class Album {
    
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $releaseDate;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverImageUrl;
    
    /**
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="albums")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     **/
    private $profile;
    
    /**
     * @ORM\OneToMany(targetEntity="Song", mappedBy="album", cascade={"persist", "remove"})
     * @ORM\OrderBy({"trackNumber" = "ASC"})
     **/
    private $songs;
    
    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="album", cascade={"persist", "remove"})
     **/
    private $reviews;
    
    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="albums", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="album_tag")
     *
     */
    private $tags;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\OneToOne(targetEntity="AlbumImage", mappedBy="album", cascade={"persist", "remove"})
     **/
    private $image;
    
    /**
     * @ORM\Column(name="license", type="integer", nullable=true)
     */
    private $license;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $staticDirectory;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zipUrl;
        
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visits;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $downloads;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $static_zip_file_name;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $artist_name;
    
    /**
     * @ORM\ManyToMany(targetEntity="Profile", mappedBy="likedAlbums")
     **/
    private $likers;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;
    
    
    
    public function __construct() {
        $this->songs = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->isActive = false;
        $this->visits = 0;
        $this->downloads = 0;
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
     * @return Album
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
     * Set description
     *
     * @param string $description
     * @return Album
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set releaseDate
     *
     * @param string $releaseDate
     * @return Album
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;
    
        return $this;
    }

    /**
     * Get releaseDate
     *
     * @return string 
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * Set coverImageUrl
     *
     * @param string $coverImageUrl
     * @return Album
     */
    public function setCoverImageUrl($coverImageUrl)
    {
        $this->coverImageUrl = $coverImageUrl;
    
        return $this;
    }

    /**
     * Get coverImageUrl
     *
     * @return string 
     */
    public function getCoverImageUrl()
    {
        foreach($this->image->getPresentations() as $pres) {
            if($pres->getName() == 'cover') {
                return $pres->getUrl();
            }
        }
        return null;
        
        //return $this->coverImageUrl;
    }
    
    /**
     * 
     * @return ImagePresentation
     */
    public function getCoverImagePresentation() {
        foreach($this->image->getPresentations() as $pres) {
            if($pres->getName() == 'cover') {
                return $pres;
            }
        }
        return null;
    }
    
    /**
     * Get list thumbnail image url
     *
     * @return string 
     */
    public function getListThumbnailUrl()
    {
        foreach($this->image->getPresentations() as $pres) {
            $url = $pres->getUrl();
            if($pres->getName() == 'list_thumbanil' && !empty($url)) {
                return $url;
            }
        }
        return null;
        
        //return $this->coverImageUrl;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Album
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
     * @return Album
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
     * @return Album
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
     * @return Album
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
    
    /**
     * Clear Songs
     */
    public function clearSongs() {
        $this->songs = new ArrayCollection();
    }

    /**
     * Add reviews
     *
     * @param \Mozcu\MozcuBundle\Entity\Review $reviews
     * @return Album
     */
    public function addReview(\Mozcu\MozcuBundle\Entity\Review $reviews)
    {
        $this->reviews[] = $reviews;
    
        return $this;
    }

    /**
     * Remove reviews
     *
     * @param \Mozcu\MozcuBundle\Entity\Review $reviews
     */
    public function removeReview(\Mozcu\MozcuBundle\Entity\Review $reviews)
    {
        $this->reviews->removeElement($reviews);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Add tags
     *
     * @param \Mozcu\MozcuBundle\Entity\Tag $tags
     * @return Album
     */
    public function addTag(\Mozcu\MozcuBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    
        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Mozcu\MozcuBundle\Entity\Tag $tags
     */
    public function removeTag(\Mozcu\MozcuBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
    
    public function getTagsCommaSeparated() {
        if($this->tags->isEmpty()) {
            return '';
        }
        
        $tagArray = array();
        foreach($this->tags as $tag) {
            $tagArray[] = $tag->getName();
        }
        return implode(',',$tagArray);
    }

    /**
     * Set image
     *
     * @param \Mozcu\MozcuBundle\Entity\AlbumImage $image
     * @return Album
     */
    public function setImage(\Mozcu\MozcuBundle\Entity\AlbumImage $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Mozcu\MozcuBundle\Entity\AlbumImage 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set license
     *
     * @param integer $license
     * @return Album
     */
    public function setLicense($license)
    {
        $this->license = $license;
    
        return $this;
    }

    /**
     * Get license
     *
     * @return integer 
     */
    public function getLicense()
    {
        return $this->license;
    }
    
    /**
     * Get User
     *
     * @return User
     */
    public function getUser() {
        return $this->profile->getUser();
    }

    /**
     * Set staticDirectory
     *
     * @param string $staticDirectory
     * @return Album
     */
    public function setStaticDirectory($staticDirectory)
    {
        $this->staticDirectory = $staticDirectory;
    
        return $this;
    }

    /**
     * Get staticDirectory
     *
     * @return string 
     */
    public function getStaticDirectory()
    {
        return $this->staticDirectory;
    }

    /**
     * Set zipUrl
     *
     * @param string $zipUrl
     * @return Album
     */
    public function setZipUrl($zipUrl)
    {
        $this->zipUrl = $zipUrl;
    
        return $this;
    }

    /**
     * Get zipUrl
     *
     * @return string 
     */
    public function getZipUrl()
    {
        return $this->zipUrl;
    }

    /**
     * Set visits
     *
     * @param integer $visits
     * @return Album
     */
    public function setVisits($visits)
    {
        $this->visits = $visits;
    
        return $this;
    }

    /**
     * Get visits
     *
     * @return integer 
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Set downloads
     *
     * @param integer $downloads
     * @return Album
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;
    
        return $this;
    }

    /**
     * Get downloads
     *
     * @return integer 
     */
    public function getDownloads()
    {
        return $this->downloads;
    }
    
    public function getLicenseUgly() {
        switch($this->license) {
            case 1:
                return 'uno';
                break;
            case 2:
                return 'dos';
                break;
            case 3:
                return 'tres';
                break;
            case 4:
                return 'cuatro';
                break;
            case 5:
                return 'cinco';
                break;
            case 6:
                return 'seis';
                break;
            case 7:
                return 'siete';
                break;
            default:
                break;
        }
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return boolean
     */
    public function belongToProfile(Profile $profile) {
        return $this->getProfile()->sameAs($profile);
    }

    /**
     * Set static_zip_file_name
     *
     * @param string $staticZipFileName
     * @return Album
     */
    public function setStaticZipFileName($staticZipFileName)
    {
        $this->static_zip_file_name = $staticZipFileName;
    
        return $this;
    }

    /**
     * Get static_zip_file_name
     *
     * @return string 
     */
    public function getStaticZipFileName()
    {
        return $this->static_zip_file_name;
    }

    /**
     * Set artist_name
     *
     * @param string $artistName
     * @return Album
     */
    public function setArtistName($artistName)
    {
        $this->artist_name = $artistName;
    
        return $this;
    }

    /**
     * Get artist_name
     *
     * @return string 
     */
    public function getArtistName()
    {
        if(is_null($this->artist_name)) {
            return $this->getProfile()->getCurrentName();
        }
        return $this->artist_name;
    }
    
    /**
     * 
     * @return ArrayCollection
     */
    public function getLikers()
    {
        return $this->likers;
    }
    
    /**
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $liker
     * @return Album
     */
    public function addLiker(Profile $liker)
    {
        $this->likers[] = $liker;
    
        return $this;
    }

    /**
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $liker
     */
    public function removeLiker(Profile $liker)
    {
        $this->likers->removeElement($liker);
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return boolean
     */
    public function sameAs(Album $album)
    {
        return $this->id == $album->getId();
    }
    
    /**
     * 
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    
    /**
     * 
     * @param string $slug
     * @return \Mozcu\MozcuBundle\Entity\Album
     */
    public function setSlug($slug) {
        $this->slug = $slug;
        
        return $this;
    }
}