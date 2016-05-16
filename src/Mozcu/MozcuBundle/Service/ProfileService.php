<?php

namespace Mozcu\MozcuBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Constraints\Url as UrlConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Mozcu\MozcuBundle\Service\ImageService;
use Mozcu\MozcuBundle\Service\UserService;
use Mozcu\MozcuBundle\Exception\AppException;
use Mozcu\MozcuBundle\Entity\Profile;
use Mozcu\MozcuBundle\Entity\ProfileLink;
use Mozcu\MozcuBundle\Entity\Album;

class ProfileService extends BaseService 
{

    /**
     *
     * @var ImageService
     */
    protected $imageService;

    /**
     *
     * @var UserService 
     */
    protected $userService;

    /**
     *
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(EntityManager $entityManager, ImageService $imageService, UserService $userService, ValidatorInterface $validator) 
    {
        parent::__construct($entityManager);
        $this->imageService = $imageService;
        $this->userService = $userService;
        $this->validator = $validator;
        $this->currentStaticDirectory = null;
    }

    public function updateProfile(Profile $profile, $data) 
    {
        try {
            if (!empty($data['name'])) {
                $profile->setName($data['name']);
            }

            if (!empty($data['slogan'])) {
                $profile->setSlogan($data['slogan']);
            }

            if (!empty($data['description'])) {
                $profile->setDescription($data['description']);
            }

            if (!empty($data['paypalEmail'])) {
                $profile->setPaypalEmail($data['paypalEmail']);
            }

            if (!empty($data['country'])) {
                $country = $this->em->getRepository('MozcuMozcuBundle:Country')->find($data['country']);
                if (empty($country)) {
                    throw new AppException('Invalid country id');
                }
                $profile->setCountry($country);
            }

            if (!empty($data['city'])) {
                $profile->setCity($data['city']);
            }

            if (!empty($data['password'])) {
                $this->userService->updateUser($profile->getUser(), $data['slug'], $data['email'], $data['password'], false);
            } else {
                $this->userService->updateUser($profile->getUser(), $data['slug'], $data['email'], null, false);
            }

            if (!empty($data['links'])) {
                foreach ($profile->getLinks() as $link) {
                    $profile->removeLink($link);
                    $this->getEntityManager()->remove($link);
                }

                foreach ($data['links'] as $linkData) {
                    $link = new ProfileLink();
                    $link->setName($linkData['name']);
                    $link->setUrl($linkData['url']);
                    $link->setProfile($profile);
                    $profile->addLink($link);
                }
            }

            if (!empty($data['image'])) {
                $image = $this->imageService->createProfileImage($data['image']);

                foreach ($profile->getImages() as $i) {
                    foreach ($i->getPresentations() as $p) {
                        $this->getEntityManager()->remove($p);
                    }
                    $this->getEntityManager()->remove($i);
                }

                $profile->addImage($image);
                $image->setProfile($profile);
            }

            $this->getEntityManager()->flush();

            return $profile;
        } catch (\Exception $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @param array $accountData
     * @return array
     */
    public function validateAccountData(Profile $profile, array $accountData) {
        $response = array('success' => false);

        if (!$this->validateEmail($accountData['email'])) {
            $response['message'] = "El formato del email es invalido: {$accountData['email']}";
        }
        if ($profile->getUser()->getUsername() != $accountData['slug'] && !$this->userService->checkUsernameDisponibility($accountData['slug'])) {
            $response['message'] = "El nombre {$accountData['slug']} esta siendo utilizado";
        }
        if ($profile->getUser()->getEmail() != $accountData['email'] && !$this->userService->checkEmailDisponibility($accountData['email'])) {
            $response['message'] = "Ya existe una cuenta con el email {$accountData['email']}";
        }

        if (isset($accountData['links']) && !empty($accountData['links'])) {
            foreach ($accountData['links'] as $linkData) {
                if (!$this->validateUrl($linkData['url'])) {
                    $response['message'] = "Formato de link invalido: {$linkData['url']}";
                }
            }
        }

        if (!$this->validateEmail($accountData['paypalEmail'])) {
            $response['message'] = "El formato del email es invalido: {$accountData['paypalEmail']}";
        }

        if (!isset($response['message'])) {
            $response['success'] = true;
        }

        return $response;
    }

    private function validateEmail($email) {
        $emailConstraint = new EmailConstraint();
        $errors = $this->validator->validateValue($email, $emailConstraint);

        if (count($errors) > 0) {
            return false;
        }
        return true;
    }

    private function validateUrl($url) {
        $urlConstraint = new UrlConstraint();
        $errors = $this->validator->validateValue($url, $urlConstraint);

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
