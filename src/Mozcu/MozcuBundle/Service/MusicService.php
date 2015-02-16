<?php

namespace Mozcu\MozcuBundle\Service;

use Mozcu\MozcuBundle\Entity\Album,
    Mozcu\MozcuBundle\Entity\Song,
    Mozcu\MozcuBundle\Entity\Tag,
    Mozcu\MozcuBundle\Entity\Profile,
    Mozcu\MozcuBundle\Entity\AlbumImage,
    Mozcu\MozcuBundle\Entity\ImagePresentation;

use Mozcu\MozcuBundle\Exception\AppException;
use Mozcu\MozcuBundle\Exception\ServiceException;
use Mozcu\MozcuBundle\Service\UploadService;
use Mozcu\MozcuBundle\Factory\AlbumFactory;

use \Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use GetId3\GetId3Core as GetId3;

class MusicService extends BaseService{
    
    /**
     *
     * @var UploadService 
     */
    private $uploadService;
    
    /**
     *
     * @var Container
     */
    private $container;
    
    public function __construct(EntityManager $entityManager, Container $container) {
        parent::__construct($entityManager);
        $this->container = $container;
        $this->uploadService = $this->container->get('mozcu_mozcu.upload_service');
    }
    
    public function toString() {
        return 'MusicService';
    }
    
    public function createAlbum(Profile $profile, array $data) {
        try {
            $factory = new AlbumFactory($this->container);
            $factory->setName($data['name'])
                    ->setArtistName($data['artist'])
                    ->setLicense($data['license'])
                    ->setDescription($data['description'])
                    ->setImageFileName($data['image_file_name'])
                    ->setProfile($profile)
                    ->setReleaseDate($data['release_date'])
                    ->setSongs($data['songs'])
                    ->setTags($data['tags']);
            $album = $factory->create();
            
            $this->getEntityManager()->persist($album);
            $this->getEntityManager()->flush();
            
            return $album;
            
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param array $data
     * @return Mozcu\MozcuBundle\Entity\Album
     * @throws AppException
     */
    public function updateAlbum(Album $album, array $data) {
        try {
            $album->setName($data['name'])
                    ->setLicense($data['license'])
                    ->setArtistName($data['artist'])
                    ->setDescription($data['description'])
                    ->setReleaseDate($data['release_date']);
            
            if(!empty($data['image_file_name'])) {
                $imageToDelete = $album->getImage();
                $this->getEntityManager()->remove($imageToDelete);
                
                $image = $this->container->get('mozcu_mozcu.image_service')->createAlbumImage($data['image_file_name']);
                $album->setImage($image);
                $image->setAlbum($album);
            }
            
            $this->removeOldSongs($album, $data['songs']);
            $this->updateSongs($album, $data['songs']);
            $this->createNewSongs($album, $data['songs']);
            
            $this->updateTags($album, $data['tags']);
            
            $this->getEntityManager()->persist($album);
            $this->getEntityManager()->flush();
            
            return $album;
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param array $songsData
     * @return array
     */
    private function getSongIdsToUpdate(array $songsData) {
        return array_map(function($songData) { 
            if(array_key_exists('id', $songData)) {
                return $songData['id'];
            }
        }, $songsData);
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param array $songsData
     */
    private function removeOldSongs(Album $album, array $songsData) {
        $toPreserve = $this->getSongIdsToUpdate($songsData);
        
        foreach($album->getSongs() as $song) {
            if(!in_array($song->getId(), $toPreserve)){
                $album->removeSong($song);
                $this->getEntityManager()->remove($song);
            }
        }
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param array $songsData
     * @throws ServiceException
     */
    private function updateSongs(Album $album, array $songsData) {
        foreach($songsData as $songData) {
            if(!array_key_exists('id', $songData)) {
                continue;
            }
            $song = $this->getEntityManager()->getRepository('MozcuMozcuBundle:Song')
                    ->findOneBy(array('id' => $songData['id'], 'album' => $album->getId()));
            
            if(is_null($song)) {
                throw new ServiceException('Invalid song id');
            }
            
            $song->setName($songData['name'])
                 ->setTrackNumber($songData['track_number']);
            
            $this->getEntityManager()->persist($song);
        }
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param array $songsData
     */
    private function createNewSongs(Album $album, array $songsData) {
        $albumFactory = new AlbumFactory($this->container);
        foreach($songsData as $songData) {
            if(!array_key_exists('id', $songData)) {
                $song = $albumFactory->createSong($songData);
                $album->addSong($song);
                $song->setAlbum($album);
            }
        }
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
            $albumFactory = new AlbumFactory($this->container);
            $this->removeTagsFromAlbum($album);
            $tagRepository = $this->getEntityManager()->getRepository('MozcuMozcuBundle:Tag');
            foreach($tagsData as $tagData) {
                $tag = $tagRepository->findOneBy(array('name' => $tagData['name']));
                if(is_null($tag)) {
                    $tag = $albumFactory->createTag($tagData);
                }
                $album->addTag($tag);
                $tag->addAlbum($album);
            }
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     */
    private function removeTagsFromAlbum(Album $album) {
        if(!$album->getTags()->isEmpty()) {
            foreach($album->getTags() as $tag) {
                $album->removeTag($tag);
            }

            $this->getEntityManager()->persist($album);
        }
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