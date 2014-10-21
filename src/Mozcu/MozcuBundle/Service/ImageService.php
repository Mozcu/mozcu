<?php

namespace Mozcu\MozcuBundle\Service;

use \Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Mozcu\MozcuBundle\Service\BaseService;

use Mozcu\MozcuBundle\Entity\AlbumImage,
    Mozcu\MozcuBundle\Entity\ImagePresentation;

class ImageService extends BaseService {
    
    /**
     *
     * @var Container
     */
    private $container;
    
    public function __construct(EntityManager $entityManager, Container $container) {
        parent::__construct($entityManager);
        $this->container = $container;
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
        $ip->setUrl('');
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
        return array(
            $this->container->getParameter('image_presentation.album_list_thumbnail_size'),
            $this->container->getParameter('image_presentation.album_header_size'),
            $this->container->getParameter('image_presentation.livesearch_size'),
            $this->container->getParameter('image_presentation.album_file_size'),
        );
    }
}
