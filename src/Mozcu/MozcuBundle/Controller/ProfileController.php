<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Mozcu\MozcuBundle\Entity\User;
use Mozcu\MozcuBundle\Entity\Profile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ProfileController extends MozcuController
{
    
    /**
     * 
     * @param string $template
     * @param arrray $parameters
     * @param array $parametersForTemplate
     * @return Response
     */
    private function renderTemplateForRequest($template, array $parameters, array $parametersForTemplate = array()) {
        $parameters['template'] = $template;
        $parameters['parameters'] = $parametersForTemplate;
        
        return $this->render('MozcuMozcuBundle:Profile:templateForRequest.html.twig', $parameters);
    }
    
    public function loadUserHeaderAction(User $user, $selectedOption) {
        $parameters['loggedInUser'] = $this->getUser();
        $parameters['user'] = $user;
        $parameters['selectedOption'] = $selectedOption;
        
        return $this->render('MozcuMozcuBundle:Profile:_profileHeader.html.twig', $parameters);
    }
    
    /**
     * 
     * @param string $username
     * @return User
     * @throws NotFoundHttpException
     */
    private function getProfileUser($username)
    {
        $user = $this->getRepository('MozcuMozcuBundle:User')->findOneBy([
            'username' => $username,
            'status' => User::STATUS_ACTIVE
        ]);
        
        if (empty($user)) {
            // todo: resolver bien el tema de pantallas de error
            //throw new NotFoundHttpException(sprintf('User %s not found.', $username));
        }
        
        return $user;
    }
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($username) {
        $user = $this->getProfileUser($username);
        
        if(!empty($user)) {
            $parameters = array('user' => $user, 'selectedOption' => 'biography');
            $template = 'MozcuMozcuBundle:Profile:_profileBiography.html.twig';
            if($this->getRequest()->isXmlHttpRequest()) {
                $parameters['parameters'] = $parameters;
                $parameters['template'] = $template;
                return $this->renderAjaxResponse('MozcuMozcuBundle:Profile:templateForAjax.html.twig', $parameters);
            }
            return $this->renderTemplateForRequest($template, $parameters);
            
        } else {
            return new Response("Pagina no encontrada", 404);
        }
    }
    
    /**
     * Este action es ejecutado cuando la pagina de upload redirige luego de la subida del album
     * TODO: kill it with fire
     * 
     * @param string $username
     * @return Response
     */
    public function albumsForProfileAction($username) {
        $user = $this->getProfileUser($username);
        
        if(!empty($user)) {
            $albums = $this->getRepository('MozcuMozcuBundle:Album')->findByProfile($user->getCurrentProfile());
            $parameters = array('user' => $user, 'albums' => $albums, 'selectedOption' => 'albums');
            $template = 'MozcuMozcuBundle:Profile:_profileAlbums.html.twig';
            $parameters['likedAlbums'] = $this->getRepository('MozcuMozcuBundle:Album')->findLikedAlbums($user->getProfile());
            
            $loggedInUser = $this->getUser();
            $parameters['isAuthenticated'] = false;
            if(!empty($loggedInUser) && $loggedInUser->getId() == $user->getId()) {
                $parameters['isAuthenticated'] = true;
            }
            
            if($this->getRequest()->isXmlHttpRequest()) {
                $parameters['parameters'] = $parameters;
                $parameters['template'] = $template;
                return $this->renderAjaxResponse('MozcuMozcuBundle:Profile:templateForAjax.html.twig', $parameters);
            }
            return $this->renderTemplateForRequest($template, $parameters);
            
        } else {
            return new Response("Pagina no encontrada", 404);
        }
    }
    
    /**
     * 
     * @param string $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function biographyAction($username) {
        //Ejemplo para manipular el lenguaje
        //$request = $this->getRequest();
        //$request->setLocale('en');
        
        $user = $this->getProfileUser($username);
        
        if(!empty($user)) {
            $template = 'MozcuMozcuBundle:Profile:_profileBiography.html.twig';
            $parameters = array('user' => $user, 'selectedOption' => 'biography');
            
            if($this->getRequest()->isXmlHttpRequest()) {
                return $this->renderAjaxResponse($template, $parameters);
            } else {
               return $this->renderTemplateForRequest($template, $parameters);
            }
        } else {
            return new Response("Usuario $username no encontrado", 404);
        }
    }
    
    /**
     * 
     * @param string $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function albumsAction($username) {
        $user = $this->getProfileUser($username);
        
        if(!empty($user)) {
            $albums = $this->getRepository('MozcuMozcuBundle:Album')->findByProfile($user->getCurrentProfile());
            $likedAlbums = $this->getRepository('Mozcu\MozcuBundle\Entity\Album')->findLikedAlbums($user->getProfile());
            
            $template = 'MozcuMozcuBundle:Profile:_profileAlbums.html.twig';
            $parameters = array('user' => $user, 'selectedOption' => 'albums', 'albums' => $albums, 
                                'likedAlbums' => $likedAlbums, 'isAuthenticated' => false);
            
            $loggedInUser = $this->getUser(); 
            $parameters['isAuthenticated'] = false;
            if(!empty($loggedInUser) && $loggedInUser->getId() == $user->getId()) {
                $parameters['isAuthenticated'] = true;
            }

            if($this->getRequest()->isXmlHttpRequest()) {
                return $this->renderAjaxResponse($template, $parameters);
            } else {
               return $this->renderTemplateForRequest($template, $parameters);
            }
            
        } else {
            return new Response("Usuario $username no encontrado", 404);
        }
    }
    
    /**
     * 
     * @param string $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function playlistsAction($username) {
        $user = $this->getProfileUser($username);
        
        if(!empty($user)) {
            
            $template = 'MozcuMozcuBundle:Profile:_profilePlaylists.html.twig';
            $parameters = array('user' => $user, 'selectedOption' => 'playlists');
            
            if($this->getRequest()->isXmlHttpRequest()) {
                return $this->renderAjaxResponse($template, $parameters);
            } else {
               return $this->renderTemplateForRequest($template, $parameters);
            }
            
        } else {
            return new Response("Usuario $username no encontrado", 404);
        }
    }
    
    /**
     * 
     * @param string $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reviewsAction($username) {
        $user = $this->getProfileUser($username);
        
        if(!empty($user)) {
            
            $template = 'MozcuMozcuBundle:Profile:_profileReviews.html.twig';
            $parameters = array('user' => $user, 'selectedOption' => 'reviews');
            
            if($this->getRequest()->isXmlHttpRequest()) {
                return $this->renderAjaxResponse($template, $parameters);
            } else {
               return $this->renderTemplateForRequest($template, $parameters);
            }
            
        } else {
            return new Response("Usuario $username no encontrado", 404);
        }
    }
    
    /**
     * 
     * @param string $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function followersAction($username) {
        $user = $this->getProfileUser($username);
        
        if(!empty($user)) {
            $path = explode('/', $this->getRequest()->getPathInfo());
            $template = 'MozcuMozcuBundle:Profile:_profileFollowers.html.twig';
            $parameters = array('user' => $user, 'selectedOption' => 'followers', 'loggedInUser' => $this->getUser(), 'subOption' => $path[2]);
            
            if($this->getRequest()->isXmlHttpRequest()) {
                return $this->renderAjaxResponse($template, $parameters);
            } else {
               return $this->renderTemplateForRequest($template, $parameters);
            }
            
        } else {
            return new Response("Usuario $username no encontrado", 404);
        }
    }
    
    public function accountAction() {
        $user = $this->getUser(); 
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->renderAjaxResponse('MozcuMozcuBundle:Profile:accountAjax.html.twig', array('user' => $user));
        }
        return $this->render('MozcuMozcuBundle:Profile:account.html.twig', array('user' => $user));
    }
    
    public function saveAccountAction(Request $request) {
        if($request->isXmlHttpRequest()) {
            $profile = $this->getUser()->getProfile();
            $accountData = $request->get('account');
            
            try {
                $validation = $this->getProfileService()->validateAccountData($profile, $accountData);
                if($validation['success']) {
                    $this->getProfileService()->updateProfile($profile, $accountData);
                    $validation['callback_url'] = $this->generateUrl('MozcuMozcuBundle_profile', 
                                                                      array('username' => $this->getUser()->getUsername()));
                }
                return $this->getJSONResponse($validation);
            } catch(\Exception $e) {
                return $this->getJSONResponse(array('success' => false, 'message' => $e->getMessage()));
            }
        } else {
            throw new BadRequestHttpException();
        }
    }
    
    public function followAction(Request $request) {
        $user = $this->getUser();
        $profileId = $request->get('profileId');
        $profile = $this->getRepository('MozcuMozcuBundle:Profile')->find($profileId);
        
        if(empty($user) || empty($profile) || !$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        if(!$user->getProfile()->following($profile)) {
            $this->getProfileService()->followProfile($user->getProfile(), $profile);
            return $this->getJSONResponse(array('success' => true, 'followers_count' => $profile->getFollowers()->count(), 
                                                'action' => 'following'));
        } else {
            $this->getProfileService()->unfollowProfile($user->getProfile(), $profile);
            return $this->getJSONResponse(array('success' => true, 'followers_count' => $profile->getFollowers()->count(),
                                                'action' => 'follow'));
        }
    }
    
    public function likeAlbumAction(Request $request) {
        $user = $this->getUser();
        $album = $this->getRepository('MozcuMozcuBundle:Album')->find($request->get('albumId'));
        
        if(empty($user) || empty($album) || !$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $profile = $user->getProfile();
        if(!$profile->likeAlbum($album)) {
            $this->getProfileService()->likeAlbum($profile, $album);
            return $this->getJSONResponse(array('success' => true, 'album_likes' => $album->getLikers()->count(), 
                                                'action' => 'unlike'));
        } else {
            $this->getProfileService()->unlikeAlbum($profile, $album);
            return $this->getJSONResponse(array('success' => true, 'album_likes' => $album->getLikers()->count(),
                                                'action' => 'like'));
        }
    }
}
