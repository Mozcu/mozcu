<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Entity\ProfileRepository")
 * @ORM\Table(name="profile")
 */
class Profile {
    
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
    private $slogan;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $webSiteUrl;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="profiles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;
        
    /**
     * @ORM\OneToMany(targetEntity="Album", mappedBy="profile", cascade={"persist"})
     **/
    private $albums;
    
    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="profile", cascade={"persist"})
     **/
    private $reviews;
    
    /**
     * @ORM\OneToMany(targetEntity="Playlist", mappedBy="profile", cascade={"persist"})
     **/
    private $playlists;
    
    /**
     * @ORM\OneToMany(targetEntity="ProfileImage", mappedBy="profile", cascade={"persist"})
     **/
    private $images;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="profiles")
     * @ORM\JoinTable(name="profile_tag")
     *
     */
    private $tags;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paypalEmail;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;
    
    /**
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     **/
    private $country;
    
    /**
     * @ORM\OneToMany(targetEntity="ProfileLink", mappedBy="profile", cascade={"persist"})
     **/
    private $links;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="Profile", mappedBy="following")
     * @var ArrayCollection
     **/
    private $followers;
    
    /**
     * @ORM\ManyToMany(targetEntity="Profile", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="following",
     *      joinColumns={@ORM\JoinColumn(name="profile_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="following_profile_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection
     **/
    private $following;
    
    /**
     * @ORM\ManyToMany(targetEntity="Album", inversedBy="likers")
     * @ORM\JoinTable(name="likers_albums")
     **/
    private $likedAlbums;
    
    public function __construct() {
        $this->albums = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->playlists = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
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
     * @return Profile
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
     * 
     * @return string
     */
    public function getCurrentName() {
        if(is_null($this->name)) {
            return $this->user->getUsername();
        } 
        return $this->name;
    }
    
    /**
     * 
     * @return string
     */
    public function getUsername() {
        return $this->user->getUsername();
    }

    /**
     * Set slogan
     *
     * @param string $slogan
     * @return Profile
     */
    public function setSlogan($slogan)
    {
        $this->slogan = $slogan;
    
        return $this;
    }

    /**
     * Get slogan
     *
     * @return string 
     */
    public function getSlogan()
    {
        return $this->slogan;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Profile
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
     * Set webSiteUrl
     *
     * @param string $webSiteUrl
     * @return Profile
     */
    public function setWebSiteUrl($webSiteUrl)
    {
        $this->webSiteUrl = $webSiteUrl;
    
        return $this;
    }

    /**
     * Get webSiteUrl
     *
     * @return string 
     */
    public function getWebSiteUrl()
    {
        return $this->webSiteUrl;
    }

    /**
     * Set user
     *
     * @param \Mozcu\MozcuBundle\Entity\User $user
     * @return Profile
     */
    public function setUser(\Mozcu\MozcuBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Mozcu\MozcuBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add albums
     *
     * @param \Mozcu\MozcuBundle\Entity\Album $albums
     * @return Profile
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

    /**
     * Add reviews
     *
     * @param \Mozcu\MozcuBundle\Entity\Review $reviews
     * @return Profile
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
     * Add playlists
     *
     * @param \Mozcu\MozcuBundle\Entity\Playlist $playlists
     * @return Profile
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
     * Add tags
     *
     * @param \Mozcu\MozcuBundle\Entity\Tag $tags
     * @return Profile
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

    /**
     * Add images
     *
     * @param \Mozcu\MozcuBundle\Entity\ProfileImage $images
     * @return Profile
     */
    public function addImage(\Mozcu\MozcuBundle\Entity\ProfileImage $images)
    {
        $this->images[] = $images;
    
        return $this;
    }

    /**
     * Remove images
     *
     * @param \Mozcu\MozcuBundle\Entity\ProfileImage $images
     */
    public function removeImage(\Mozcu\MozcuBundle\Entity\ProfileImage $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }
    
    /**
     * 
     * @return ProfileImage
     */
    public function getMainImage() {
        foreach($this->images as $image) {
            if($image->getMain()) {
                return $image;
            }
        }
        
        return null;
    }
    
    /**
     *
     * @return string
     */
    public function getProfileImageUrlForHeader() {
        $image = $this->getMainImage();
        if(!empty($image)) {
            foreach($image->getPresentations() as $p) {
                if($p->getName() == 'profile_header') {
                    return $p->getUrl();
                }
            }
        }
    }

    /**
     * Set paypalEmail
     *
     * @param string $paypalEmail
     * @return Profile
     */
    public function setPaypalEmail($paypalEmail)
    {
        $this->paypalEmail = $paypalEmail;
    
        return $this;
    }

    /**
     * Get paypalEmail
     *
     * @return string 
     */
    public function getPaypalEmail()
    {
        return $this->paypalEmail;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Profile
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param \Mozcu\MozcuBundle\Entity\Country $country
     * @return Profile
     */
    public function setCountry(\Mozcu\MozcuBundle\Entity\Country $country = null)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \Mozcu\MozcuBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return boolean
     */
    public function sameAs(Profile $profile) {
        return $this->getId() == $profile->getId();
    }
    
    /**
     * 
     * @return array
     */
    public function getArtists() {
        $artists = array();
        foreach($this->albums as $album) {
            if($album->getIsActive() && !in_array($album->getArtistName(), $artists)) {
                $artists[] = $album->getArtistName();
            }
        }
        return $artists;
    }

    /**
     * Add links
     *
     * @param \Mozcu\MozcuBundle\Entity\ProfileLink $links
     * @return Profile
     */
    public function addLink(\Mozcu\MozcuBundle\Entity\ProfileLink $links)
    {
        $this->links[] = $links;
    
        return $this;
    }

    /**
     * Remove links
     *
     * @param \Mozcu\MozcuBundle\Entity\ProfileLink $links
     */
    public function removeLink(\Mozcu\MozcuBundle\Entity\ProfileLink $links)
    {
        $this->links->removeElement($links);
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLinks()
    {
        return $this->links;
    }
    
    /**
     * 
     * @return ArrayCollection
     */
    public function getFollowers() {
        return $this->followers;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return \Mozcu\MozcuBundle\Entity\Profile
     */
    public function addFollower(Profile $profile) {
        $this->followers->add($profile);
        
        return $this;
    }
    
    public function removeFollower(Profile $profile) {
        $this->followers->removeElement($profile);
    }
    
    /**
     * 
     * @return ArrayCollection
     */
    public function getFollowing() {
        return $this->following;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return \Mozcu\MozcuBundle\Entity\Profile
     */
    public function addFollowing(Profile $profile) {
        $this->following->add($profile);
        
        return $this;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     */
    public function removeFollowing(Profile $profile) {
        $this->following->removeElement($profile);
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return boolean
     */
    public function following(Profile $profile) {
        foreach($this->following as $following) {
            if($profile->sameAs($following)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 
     * @return ArrayCollection
     */
    public function getLikedAlbums()
    {
        return $this->likedAlbums;
    }
    
    /**
     *
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return Profile
     */
    public function addLikedAlbum(Album $album)
    {
        $this->likedAlbums[] = $album;
    
        return $this;
    }

    /**
     *
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     */
    public function removeLikedAlbum(Album $album)
    {
        $this->likedAlbums->removeElement($album);
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return boolean
     */
    public function likeAlbum(Album $album) 
    {
        foreach($this->likedAlbums as $likedAlbum) {
            if($album->sameAs($likedAlbum)) {
                return true;
            }
        }
        
        return false;
    }
}