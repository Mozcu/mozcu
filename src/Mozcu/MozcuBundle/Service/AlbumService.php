<?php

namespace Mozcu\MozcuBundle\Service;

// Entities
use Mozcu\MozcuBundle\Entity\Album,
    Mozcu\MozcuBundle\Entity\Profile;

// Exception
use Mozcu\MozcuBundle\Exception\AppException;
use Mozcu\MozcuBundle\Exception\ServiceException;

// Services Helpers Factories
use Mozcu\MozcuBundle\Service\UploadService;
use Mozcu\MozcuBundle\Factory\AlbumFactory;
use Mozcu\MozcuBundle\Helper\StringHelper;
use Mozcu\MozcuBundle\Service\ImageService;

// Vendor
use Doctrine\ORM\EntityManager;

class AlbumService extends BaseService
{
    
    /**
     *
     * @var UploadService 
     */
    private $uploadService;
    
    /**
     *
     * @var ImageService
     */
    private $imageService;
    
    /**
     *
     * @var array
     */
    private $uploadsData;
    
    /**
     *
     * @var array
     */
    private $googleApiData;
    
    public function __construct(EntityManager $entityManager, UploadService $uploadService, ImageService $imageService, array $uploadsData, array $googleApiData) 
    {
        parent::__construct($entityManager);
        $this->uploadService = $uploadService;
        $this->imageService = $imageService;
        $this->uploadsData = $uploadsData;
        $this->googleApiData = $googleApiData;
    }
    
    public function toString() 
    {
        return 'MusicService';
    }
    
    public function createAlbum(Profile $profile, array $data) 
    {
        try {
            $factory = new AlbumFactory($this->em, $this->imageService, $this->uploadsData);
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
            
            $slug = $this->generateSlug($data['name'], $profile);
            $album->setSlug($slug);
            
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
    public function updateAlbum(Album $album, array $data) 
    {
        try {
            $oldName = $album->getName();
            
            $album->setName($data['name'])
                    ->setLicense($data['license'])
                    ->setArtistName($data['artist'])
                    ->setDescription($data['description'])
                    ->setReleaseDate($data['release_date']);
            
            if(!empty($data['image_file_name'])) {
                $imageToDelete = $album->getImage();
                $this->getEntityManager()->remove($imageToDelete);
                
                $image = $this->imageService->createAlbumImage($data['image_file_name']);
                $album->setImage($image);
                $image->setAlbum($album);
            }
            
            $this->removeOldSongs($album, $data['songs']);
            $this->updateSongs($album, $data['songs']);
            $this->createNewSongs($album, $data['songs']);
            
            $this->updateTags($album, $data['tags']);
            
            if($oldName != $data['name']) {
                $slug = $this->generateSlug($data['name'], $album->getProfile());
                $album->setSlug($slug);
            }
            
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
    private function getSongIdsToUpdate(array $songsData) 
    {
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
    private function removeOldSongs(Album $album, array $songsData) 
    {
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
    private function updateSongs(Album $album, array $songsData) 
    {
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
    private function createNewSongs(Album $album, array $songsData) 
    {
        $albumFactory = new AlbumFactory($this->em, $this->imageService, $this->uploadsData);
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
    private function updateTags(Album $album, array $tagsData) 
    {
        try {
            $albumFactory = new AlbumFactory($this->em, $this->imageService, $this->uploadsData);
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
    private function removeTagsFromAlbum(Album $album) 
    {
        if (!$album->getTags()->isEmpty()) {
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
    public function deleteAlbum(Album $album) 
    {
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
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     */
    public function prepareZip(Album $album) 
    {
        $response = $this->uploadService->generateZip($album);
        $baseUrl = $this->googleApiData['base_url'];
        $bucket = $this->googleApiData['storage_bucket'];
        
        $album->setZipUrl($baseUrl . $bucket . '/' . $response['name']);
        $album->setStaticZipFileName($response['name']);
        
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     */
    public function increasePlayCount(Album $album) 
    {
        $plays = $album->getVisits();
        $album->setVisits($plays + 1);
        
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     */
    public function increaseDownloadCount(Album $album) 
    {
        $downloads = $album->getDownloads();
        $album->setDownloads($downloads + 1);
        
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
    }
    
    /**
     * 
     * @param string $name
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return string
     */
    private function generateSlug($name, Profile $profile)
    {
        $slug = StringHelper::slugify($name);
        $repo = $this->getEntityManager()->getRepository('MozcuMozcuBundle:Album');
        
        $count = 0;
        $origSlug = $slug;
        do {
            $altSlug = ($count > 0) ? $origSlug . '_' . $count : $origSlug;
            $album = $repo->findOneByUsernameAndSlug($profile->getUsername(), $altSlug);
            $count++;
            $slug = $altSlug;
        } while(!is_null($album));
        
        return $slug;
    }
}