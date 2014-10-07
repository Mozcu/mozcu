<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Entities\User;
use Mozcu\MozcuBundle\Entity\Profile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


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
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($username) {
        $user = $this->getRepository('MozcuMozcuBundle:User')->findOneBy(array('username' => $username));
        
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
    
    public function albumsForProfileAction($username) {
        $user = $this->getRepository('MozcuMozcuBundle:User')->findOneBy(array('username' => $username));
        
        if(!empty($user)) {
            $albums = $this->getRepository('MozcuMozcuBundle:Album')->findByProfile($user->getCurrentProfile());
            $parameters = array('user' => $user, 'albums' => $albums, 'selectedOption' => 'albums');
            $template = 'MozcuMozcuBundle:Profile:_profileAlbums.html.twig';
            
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
        
        $user = $this->getRepository('MozcuMozcuBundle:User')->findOneBy(array('username' => $username));
        
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
        $user = $this->getRepository('MozcuMozcuBundle:User')->findOneBy(array('username' => $username));
        if(!empty($user)) {
            $albums = $this->getRepository('MozcuMozcuBundle:Album')->findByProfile($user->getCurrentProfile());
            
            $template = 'MozcuMozcuBundle:Profile:_profileAlbums.html.twig';
            $parameters = array('user' => $user, 'selectedOption' => 'albums', 'albums' => $albums, 'isAuthenticated' => false);
            
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
        $user = $this->getRepository('MozcuMozcuBundle:User')->findOneBy(array('username' => $username));
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
        $user = $this->getRepository('MozcuMozcuBundle:User')->findOneBy(array('username' => $username));
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
        $user = $this->getRepository('MozcuMozcuBundle:User')->findOneBy(array('username' => $username));
        if(!empty($user)) {
            
            $template = 'MozcuMozcuBundle:Profile:_profileFollowers.html.twig';
            $parameters = array('user' => $user, 'selectedOption' => 'followers');
            
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
    
    public function getCountriesAction(Request $request) {
        $name = $request->get('term');
        $countries = $this->getRepository('MozcuMozcuBundle:Country')->findByLikeName($name);
        
        $export = [];
        foreach($countries as $c) {
            $export[] = array('id' => $c->getId(), 'label' => $c->getName(), 'value' => $c->getName());
        }
        
        return $this->getJSONResponse($export);
    }
    
    public function getCitiesAction(Request $request) {
        $term = $request->get('term');
        $cities = $this->getRepository('MozcuMozcuBundle:Profile')->findCitiesByLike($term);
        
        $export = [];
        foreach($cities as $c) {
            $export[] = array('id' => $c['city'], 'label' => $c['city'], 'value' => $c['city']);
        }
        
        return $this->getJSONResponse($export);
    }
}
