<?php

namespace Mozcu\MozcuBundle\Factory;

use Mozcu\MozcuBundle\Entity\Album,
    Mozcu\MozcuBundle\Entity\Song,
    Mozcu\MozcuBundle\Entity\Tag,
    Mozcu\MozcuBundle\Entity\Profile;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \GetId3\GetId3Core as GetId3;

use Doctrine\ORM\EntityManager;

class AlbumFactory {
    
    /**
     *
     * @var Container
     */
    private $container;
    
    /**
     *
     * @var EntityManager
     */
    private $em;
    
    /**
     *
     * @var Profile
     */
    private $profile;
    
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var string 
     */
    private $artistName;
    
    /**
     *
     * @var int
     */
    private $license;
    
    /**
     *
     * @var string
     */
    private $description;
    
    /**
     *
     * @var string
     */
    private $releaseDate;
    
    /**
     *
     * @var string
     */
    private $imageFileName;
    
    /**
     *
     * @var array
     */
    private $songs = array();
    
    /**
     *
     * @var array
     */
    private $tags = array();
    
    /**
     * 
     * @param string $profile
     * @return \Mozcu\MozcuBundle\Factory\AlbumFactory
     */
    public function setProfile($profile) {
        $this->profile = $profile;
        return $this;
    }

    /**
     * 
     * @param string $name
     * @return \Mozcu\MozcuBundle\Factory\AlbumFactory
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @param string $artistName
     * @return \Mozcu\MozcuBundle\Factory\AlbumFactory
     */
    public function setArtistName($artistName) {
        $this->artistName = $artistName;
        return $this;
    }

    /**
     * 
     * @param int $license
     * @return \Mozcu\MozcuBundle\Factory\AlbumFactory
     */
    public function setLicense($license) {
        $this->license = $license;
        return $this;
    }

    /**
     * 
     * @param string $description
     * @return \Mozcu\MozcuBundle\Factory\AlbumFactory
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * 
     * @param string $releaseDate
     * @return \Mozcu\MozcuBundle\Factory\AlbumFactory
     */
    public function setReleaseDate($releaseDate) {
        $this->releaseDate = $releaseDate;
        return $this;
    }

    /**
     * 
     * @param string $imageFileName
     * @return \Mozcu\MozcuBundle\Factory\AlbumFactory
     */
    public function setImageFileName($imageFileName) {
        $this->imageFileName = $imageFileName;
        return $this;
    }

    /**
     * 
     * @param array $songs
     * @return \Mozcu\MozcuBundle\Factory\AlbumFactory
     */
    public function setSongs(array $songs) {
        $this->songs = $songs;
        return $this;
    }

    /**
     * 
     * @param array $tags
     * @return \Mozcu\MozcuBundle\Factory\AlbumFactory
     */
    public function setTags(array $tags) {
        $this->tags = $tags;
        return $this;
    }

    
    public function __construct(Container $container) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getEntityManager();
    }
    
    /**
     * 
     * @return \Mozcu\MozcuBundle\Entity\Album
     */
    public function create() {
        $album = new Album();
        
        $album->setName($this->name)
                ->setArtistName($this->artistName)
                ->setDescription($this->description)
                ->setProfile($this->profile)
                ->setLicense($this->license)
                ->setReleaseDate($this->releaseDate);
        
        $image = $this->container->get('mozcu_mozcu.image_service')->createAlbumImage($this->imageFileName);
        $album->setImage($image);
        $image->setAlbum($album);
        
        foreach($this->songs as $songData) {
            $song = $this->createSong($songData);
            $song->setAlbum($album);
            $album->addSong($song);
        }
        
        foreach($this->tags as $tagData) {
            $tag = $this->em->getRepository('MozcuMozcuBundle:Tag')->findOneBy(array('name' => $tagData['name']));
            if(is_null($tag)) {
                $tag = $this->createTag($tagData);
            }
            $album->addTag($tag);
            $tag->addAlbum($album);
        }
        
        return $album;
    }
    
    /**
     * 
     * @param array $data
     * @return \Mozcu\MozcuBundle\Entity\Song
     */
    public function createSong(array $data) {
        $song = new Song();
        $song->setName($data['name'])
             ->setTrackNumber($data['track_number'])
             ->setTemporalFileName($data['file_name'])
             ->setLength($this->getSongTime($data['file_name']));
        
        return $song;
    }
    
    /**
     * 
     * @param string $songName
     * @return string
     */
    public function getSongTime($songName) {
        $tmpDir = $this->container->getParameter('uploads.tmp_songs_dir');
        
        $getId3 = new GetId3();
        $audio = $getId3
            ->setOptionMD5Data(true)
            ->setOptionMD5DataSource(true)
            ->setEncoding('UTF-8')
            ->analyze("$tmpDir/$songName");
            
        if(isset($audio['playtime_seconds'])) {
            return gmdate('i:s', $audio['playtime_seconds']);
        } else {
            return null;
        }
    }
    
    /**
     * 
     * @param array $tagData
     * @return \Mozcu\MozcuBundle\Entity\Tag
     */
    public function createTag(array $tagData) {
        $tag = new Tag();
        $tag->setName($tagData['name']);
        return $tag;
    }
    
}
