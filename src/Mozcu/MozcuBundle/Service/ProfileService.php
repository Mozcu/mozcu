<?php

namespace Mozcu\MozcuBundle\Service;

use Mozcu\MozcuBundle\Entity\Profile;
use Doctrine\ORM\EntityManager;
use Mozcu\MozcuBundle\Service\UploadService;
use Mozcu\MozcuBundle\Entity\ImagePresentation;
use Mozcu\MozcuBundle\Entity\ProfileImage;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Mozcu\MozcuBundle\Exception\AppException;
use Mozcu\MozcuBundle\Entity\ProfileLink;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Constraints\Url as UrlConstraint;
use Mozcu\MozcuBundle\Entity\Album;

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
            
            if(isset($data['paypalEmail']) && !empty($data['paypalEmail'])) {
                $profile->setPaypalEmail($data['paypalEmail']);
            }
            
            if(isset($data['country']) && !empty($data['country'])) {
                $country = $this->em->getRepository('MozcuMozcuBundle:Country')->find($data['country']);
                if(empty($country)) {
                    throw new AppException('Invalid country id');
                }
                $profile->setCountry($country);
            }
            
            if(isset($data['city']) && !empty($data['city'])) {
                $profile->setCity($data['city']);
            }
            
            if(isset($data['password']) && !empty($data['password'])) {
                $this->container->get('mozcu_mozcu.user_service')->updateUser($profile->getUser(), $data['slug'], $data['email'], 
                                                                              $data['password'], false);
            } else {
                $this->container->get('mozcu_mozcu.user_service')->updateUser($profile->getUser(), $data['slug'], $data['email'], 
                                                                              null, false);
            }
            
            if(isset($data['links']) && !empty($data['links'])) {
                foreach($profile->getLinks() as $link) {
                    $profile->removeLink($link);
                    $this->getEntityManager()->remove($link);
                }
                
                foreach($data['links'] as $linkData) {
                    $link = new ProfileLink();
                    $link->setName($linkData['name']);
                    $link->setUrl($linkData['url']);
                    $link->setProfile($profile);
                    $profile->addLink($link);
                }
            }
            
            if(isset($data['image']) && !empty($data['image'])) {
                $image = $this->createImage($data['image']);
                
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
        $presentations[] = $this->container->getParameter('image_presentation.livesearch_size');
        
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
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @param array $accountData
     * @return array
     */
    public function validateAccountData(Profile $profile, array $accountData) {
        $userService = $this->container->get('mozcu_mozcu.user_service');
        
        $response =  array('success' => false);
        
        if(!$this->validateEmail($accountData['email'])) {
            $response['message'] =  "El formato del email es invalido: {$accountData['email']}";
        }
        if($profile->getUser()->getUsername() != $accountData['slug'] && !$userService->checkUsernameDisponibility($accountData['slug'])) {
            $response['message'] = "El nombre {$accountData['slug']} esta siendo utilizado";
        }
        if($profile->getUser()->getEmail() != $accountData['email'] && !$userService->checkEmailDisponibility($accountData['email'])) {
            $response['message'] = "Ya existe una cuenta con el email {$accountData['email']}";
        }
        
        if(isset($accountData['links']) && !empty($accountData['links'])) {
            foreach($accountData['links'] as $linkData) {
                if(!$this->validateUrl($linkData['url'])) {
                    $response['message'] =  "Formato de link invalido: {$linkData['url']}";
                }
            }
        }
        
        if(!$this->validateEmail($accountData['paypalEmail'])) {
            $response['message'] =  "El formato del email es invalido: {$accountData['paypalEmail']}";
        }
        
        if(!isset($response['message'])) {
            $response['success'] = true;
        }
        
        return $response;
    }
    
    private function validateEmail($email) {
        $emailConstraint = new EmailConstraint();
        $errors = $this->container->get('validator')->validateValue(
            $email,
            $emailConstraint 
        );
        
        if (count($errors) > 0) {
            return false;    
        }
        return true;
    }
    
    private function validateUrl($url) {
        $urlConstraint = new UrlConstraint();
        $errors = $this->container->get('validator')->validateValue(
            $url,
            $urlConstraint 
        );
        
        if (count($errors) > 0) {
            return false;    
        }
        return true;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @param \Mozcu\MozcuBundle\Entity\Profile $toFollow
     */
    public function followProfile(Profile $profile, Profile $toFollow) {
        $profile->addFollowing($toFollow);
        $toFollow->addFollower($profile);
        
        $this->getEntityManager()->persist($profile);
        $this->getEntityManager()->persist($toFollow);
        $this->getEntityManager()->flush();
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @param \Mozcu\MozcuBundle\Entity\Profile $toUnfollow
     */
    public function unfollowProfile(Profile $profile, Profile $toUnfollow) {
        $profile->removeFollowing($toUnfollow);
        $toUnfollow->removeFollower($profile);
        
        $this->getEntityManager()->persist($profile);
        $this->getEntityManager()->persist($toUnfollow);
        $this->getEntityManager()->flush();
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     */
    public function likeAlbum(Profile $profile, Album $album) {
        $profile->addLikedAlbum($album);
        $album->addLiker($profile);
        
        $this->getEntityManager()->persist($profile);
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     */
    public function unlikeAlbum(Profile $profile, Album $album) {
        $profile->removeLikedAlbum($album);
        $album->removeLiker($profile);
        
        $this->getEntityManager()->persist($profile);
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
    }
}