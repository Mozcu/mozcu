<?php

namespace Mozcu\MozcuBundle\Service;

use Mozcu\MozcuBundle\Entity\Profile;
use \Doctrine\ORM\EntityManager;
use Mozcu\MozcuBundle\Service\UploadService;
use Mozcu\MozcuBundle\Entity\ImagePresentation;
use Mozcu\MozcuBundle\Entity\ProfileImage;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \Mozcu\MozcuBundle\Exception\AppException;

class ProfileService extends BaseService{
    
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
        return 'ProfileService';
    }
    
    
    public function updateProfile(Profile $profile, $data) {
        try {
            if(isset($data['name']) && !empty($data['name'])) {
                $profile->setName($data['name']);
            }
            
            if(isset($data['slogan']) && !empty($data['slogan'])) {
                $profile->setSlogan($data['slogan']);
            }
            
            if(isset($data['description']) && !empty($data['description'])) {
                $profile->setDescription($data['description']);
            }
            
            if(isset($data['webSiteUrl']) && !empty($data['webSiteUrl'])) {
                $profile->setWebSiteUrl($data['webSiteUrl']);
            }
            
            if(isset($data['paypalEmail']) && !empty($data['paypalEmail'])) {
                $profile->setPaypalEmail($data['paypalEmail']);
            }
            
            if(isset($data['country']) && !empty($data['country'])) {
                $profile->setCountry($data['country']);
            }
            
            if(isset($data['city']) && !empty($data['city'])) {
                $profile->setCity($data['city']);
            }
            
            if(isset($data['imageFileName']) && !empty($data['imageFileName'])) {
                $image = $this->createImage($data['imageFileName']);
                
                foreach($profile->getImages() as $i) {
                    foreach($i->getPresentations() as $p) {
                        $this->getEntityManager()->remove($p);
                    }
                    $this->getEntityManager()->remove($i);
                }
                $this->getEntityManager()->flush();
                
                $profile->addImage($image);
                $image->setProfile($profile);
            }
            
            $this->getEntityManager()->persist($profile);
            $this->getEntityManager()->flush();
            
            return $profile;
        } catch(\Exception $e) {
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
        $presentations[] = $this->container->getParameter('image_presentation.profile_thumbnail_size');
        $presentations[] = $this->container->getParameter('image_presentation.profile_header_size');
        
        $presentationsData = $this->uploadService->uploadImageToStaticServer($tmpName, $presentations);
        //var_dump($presentationsData); die;
        $image = new ProfileImage();
        $image->setMain(true);
        $image->setCreatedAt(new \DateTime());
        
        foreach($presentationsData as $pd) {
            $ip = new ImagePresentation();
            $ip->setWidth($pd['width']);
            $ip->setHeight($pd['height']);
            $ip->setName($pd['name']);
            $ip->setUrl($pd['url']);
            $ip->setImage($image);
            $image->addPresentation($ip);
        }
        
        return $image;
    }
}