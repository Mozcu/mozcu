<?php

namespace Mozcu\MozcuBundle\Service;

use \Doctrine\ORM\EntityManager;

use Mozcu\MozcuBundle\Service\BaseService,
    Mozcu\MozcuBundle\Service\UploadService;

use Mozcu\MozcuBundle\Entity\AlbumImage,
    Mozcu\MozcuBundle\Entity\ProfileImage,
    Mozcu\MozcuBundle\Entity\ImagePresentation;

class ImageService extends BaseService {
    
    /**
     *
     * @var array
     */
    private $imageData;
    
    /**
     *
     * @var UploadService
     */
    private $uploadService;
    
    public function __construct(EntityManager $entityManager, UploadService $uploadService, array $imageData) {
        parent::__construct($entityManager);
        $this->imageData = $imageData;
        $this->uploadService = $uploadService;
    }
    
    
    /**
     * 
     * @param string $temporalFileName
     * @return \Mozcu\MozcuBundle\Entity\AlbumImage
     */
    public function createAlbumImage($temporalFileName) {
        $presentations = $this->getAlbumPresentations();
        
        $image = new AlbumImage();
        $image->setMain(true);
        $image->setCreatedAt(new \DateTime());
        $image->setTemporalFileName($temporalFileName);
        
        foreach($presentations as $data) {
            $ip = $this->createPresentation($data);
            $ip->setImage($image);
            $image->addPresentation($ip);
        }
        
        return $image;
    }
    
    public function createProfileImage($temporalFileName)
    {
        $presentationsData = $this->uploadService->uploadImageToStaticServer($temporalFileName, $this->getProfilePresentations());
        
        $image = new ProfileImage();
        $image->setMain(true);
        $image->setCreatedAt(new \DateTime());
        
        foreach($presentationsData as $data) {
            $ip = $this->createPresentation($data);
            $ip->setImage($image);
            $image->addPresentation($ip);
        }
        
        return $image;
    }
    
    /**
     * 
     * @param array $data
     * @return \Mozcu\MozcuBundle\Entity\ImagePresentation
     */
    private function createPresentation(array $data) {
        $ip = new ImagePresentation();
        $ip->setWidth($data['width']);
        $ip->setHeight($data['height']);
        $ip->setName($data['name']);
        $ip->setUrl(empty($data['url']) ? '' : $data['url']);
        
        if(isset($data['thumbnail'])) {
            $ip->setThumbnail($data['thumbnail']);
        }
        
        return $ip;
    }
    
    /**
     * 
     * @return array
     */
    private function getAlbumPresentations() {
        return [
            $this->imageData['album_list_thumbnail_size'],
            $this->imageData['album_header_size'],
            $this->imageData['livesearch_size'],
            $this->imageData['album_file_size'],
        ];
    }
    
    /**
     * 
     * @return array
     */
    private function getProfilePresentations()
    {
        return [
            $this->imageData['profile_thumbnail_size'],
            $this->imageData['profile_header_size'],
            $this->imageData['livesearch_size'],
        ];
    }
}
