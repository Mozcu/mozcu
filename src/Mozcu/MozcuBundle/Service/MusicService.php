<?php

namespace Mozcu\MozcuBundle\Service;

use \Doctrine\ORM\EntityManager;
use Mozcu\MozcuBundle\Entity\Album;
use Mozcu\MozcuBundle\Entity\Song;
use Mozcu\MozcuBundle\Entity\Tag;
use Mozcu\MozcuBundle\Entity\Profile;
use \Mozcu\MozcuBundle\Exception\AppException;
use Mozcu\MozcuBundle\Service\UploadService;
use Mozcu\MozcuBundle\Entity\AlbumImage;
use \Mozcu\MozcuBundle\Entity\ImagePresentation;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \GetId3\GetId3Core as GetId3;

class MusicService extends BaseService{
    
    /**
     *
     * @var UploadService 
     */
    private $uploadService;
    
    private $currentStaticDirectory;
    
    /**
     *
     * @var Container
     */
    private $container;
    
    public function __construct(EntityManager $entityManager, UploadService $uploadService, Container $container) {
        parent::__construct($entityManager);
        $this->uploadService = $uploadService;
        $this->container = $container;
        $this->currentStaticDirectory = null;
    }
    
    public function toString() {
        return 'MusicService';
    }
    
    public function createNewAlbum(Profile $profile, array $data) {
        try {
            $album = new Album();
            $album->setProfile($profile);
            
            return $this->updateAlbum($album, $data);
        } catch (\Exception $e) {
            throw new AppException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param array $data
     * @return Mozcu\MozcuBundle\Entity\Album
     * @throws AppException
     */
    public function updateAlbum(Album $album, array $data, $toQueue = false) {
        try {
            $album->setName($data['name']);
            $album->setLicense($data['license']);
            
            if(isset($data['description']) && !empty($data['description'])) {
                $album->setDescription($data['description']);
            }
            
            if(isset($data['release_date']) && !empty($data['release_date'])) {
                $album->setReleaseDate($data['release_date']);
            }
            
            if(!empty($data['image_file_name'])) {
                $image = $this->createImage($data['image_file_name']);
                $album->setImage($image);
                $image->setAlbum($album);
            }
            
            $album = $this->updateSongs($album, $data['songs']);
            $album = $this->updateTags($album, $data['tags']);
            
            if(!is_null($this->currentStaticDirectory)) {
                $album->setStaticDirectory($this->currentStaticDirectory);
            }
            
            if($toQueue) {
                if(!$this->compareSongs($album, $data)) {
                    $album->setIsActive(false);
                    $this->container->get('mozcu_mozcu.queue_service')->addAlbumToQueue($album, true); 
                }
            } else {
                $this->container->get('mozcu_mozcu.queue_service')->addAlbumToQueue($album); 
            }
            
            $this->getEntityManager()->persist($album);
            $this->getEntityManager()->flush();
            
            
            
            //$this->addImageToAlbumFolder($data['image_file_name'], $this->currentStaticDirectory);
            
            return $album;
            
        } catch (\Exception $e) {
            throw new AppException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param string $tmpName
     * @return \Mozcu\MozcuBundle\Entity\AlbumImage
     */
    public function createImage($tmpName) {
        $presentations = array(); 
        $presentations[] = $this->container->getParameter('image_presentation.album_list_thumbnail_size');
        $presentations[] = $this->container->getParameter('image_presentation.album_header_size');
        $presentations[] = $this->container->getParameter('image_presentation.livesearch_size');
        $presentations[] = $this->container->getParameter('image_presentation.album_file_size');
        
        $image = new AlbumImage();
        $image->setMain(true);
        $image->setCreatedAt(new \DateTime());
        $image->setTemporalFileName($tmpName);
        
        foreach($presentations as $pd) {
            $ip = new ImagePresentation();
            $ip->setWidth($pd['width']);
            $ip->setHeight($pd['height']);
            $ip->setName($pd['name']);
            $ip->setUrl('');
            if(isset($pd['thumbnail'])) {
                $ip->setThumbnail($pd['thumbnail']);
            }
            $ip->setImage($image);
            $image->addPresentation($ip);
        }
        
        return $image;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param array $songsData
     * @return \Mozcu\MozcuBundle\Entity\Album
     * @throws AppException
     */
    private function updateSongs(Album $album, array $songsData) {
        try {
            $this->removeSongsFromAlbum($album);
            
            foreach($songsData as $songData) {
                $song = $this->createSong($songData);
                $song->setAlbum($album);
                $album->addSong($song);
            }
            return $album;
        } catch (\Exception $e) {
            throw new AppException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return boolean
     */
    private function removeSongsFromAlbum(Album $album) {
        if($album->getSongs()->isEmpty()) {
            return true;
        }
        
        foreach($album->getSongs() as $song) {
            $this->getEntityManager()->remove($song);
        }
        $album->clearSongs();
        $this->getEntityManager()->flush();
        return true;
    }
    
    /**
     * 
     * @param array $songData
     * @return \Mozcu\MozcuBundle\Entity\Song
     */
    private function createSong(array $songData) {
        $song = new Song();
        $song->setName($songData['name']);
        $song->setTrackNumber($songData['track_number']);
        
        if(isset($songData['file_name']) && !empty($songData['file_name'])) {
            $song->setTemporalFileName($songData['file_name']);
            $song->setLength($this->getSongTime($songData['file_name']));
        }
                
        if(isset($songData['url']) && !empty($songData['url'])) {
            $song->setUrl($songData['url']);
        }
        
        if(isset($songData['length']) && !empty($songData['length'])) {
            $song->setLength($songData['length']);
        }
        
        return $song;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param array $tagsData
     * @return \Mozcu\MozcuBundle\Entity\Album
     * @throws AppException
     */
    private function updateTags(Album $album, array $tagsData) {
        try {
            $this->removeTagsFromAlbum($album);
            $tagRepository = $this->getEntityManager()->getRepository('MozcuMozcuBundle:Tag');
            foreach($tagsData as $tagData) {
                $tag = $tagRepository->findOneBy(array('name' => $tagData['name']));
                if(is_null($tag)) {
                    $tag = $this->createTag($tagData);
                }
                $album->addTag($tag);
                $tag->addAlbum($album);
            }
            return $album;
        } catch (\Exception $e) {
            throw new AppException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return boolean
     */
    private function removeTagsFromAlbum(Album $album) {
        if($album->getTags()->isEmpty()) {
            return true;
        }
        
        foreach($album->getTags() as $tag) {
            $album->removeTag($tag);
        }
        
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
        return true;
    }
    
    /**
     * 
     * @param array $tagData
     * @return \Mozcu\MozcuBundle\Entity\Tag
     */
    private function createTag(array $tagData) {
        $tag = new Tag();
        $tag->setName($tagData['name']);
        return $tag;
    }
    
    private function getSongTime($fileName) {
        $tmpDir = $this->container->getParameter('uploads.tmp_songs_dir');
        
        $getId3 = new GetId3();
        $audio = $getId3
            ->setOptionMD5Data(true)
            ->setOptionMD5DataSource(true)
            ->setEncoding('UTF-8')
            ->analyze("$tmpDir/$fileName");
            
        if(isset($audio['playtime_seconds'])) {
            return gmdate('i:s', $audio['playtime_seconds']);
        } else {
            return null;
        }
    }
    
    /**
     * Compara si el album posee las mismas canciones que la data ingresante
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param array $data
     * @return boolean
     */
    private function compareSongs(Album $album, $data) {
        foreach($album->getSongs() as $song) {
            $same = false;
            foreach($data['songs'] as $songData) {
                if(isset($songData['url']) && $song->getUrl() == $songData['url']) {
                    $same = true;
                    break 1;
                } 
            }
            if(!$same) {
               return false; 
            }
        }
        return true;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     */
    public function deleteAlbum(Album $album) {
        $em = $this->getEntityManager();
        
        $pending = $em->getRepository('MozcuMozcuBundle:AlbumUploadQueuePending')
                    ->findOneBy(array('album' => $album->getId()));
        
        if(!is_null($pending)) {
            $em->remove($pending);
        }
        
        $failed = $em->getRepository('MozcuMozcuBundle:AlbumUploadQueueFailed')
                    ->findOneBy(array('album' => $album->getId()));
        
        if(!is_null($failed)) {
            $em->remove($failed);
        }
        
        $this->uploadService->deleteAlbumFromStaticServer($album);
        
        $em->remove($album);
        $em->flush();
    } 
}