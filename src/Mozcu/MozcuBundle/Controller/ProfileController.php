<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Entities\User;
use Mozcu\MozcuBundle\Entity\Profile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;


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
     * @param string $template
     * @param arrray $parameters
     * @param array $parametersForTemplate
     * @return Response
     */
    private function renderAccountForRequest($template, array $parameters, array $parametersForTemplate = array()) {
        $parameters['template'] = $template;
        $parameters['parameters'] = $parametersForTemplate;
        return $this->render('MozcuMozcuBundle:Profile:accountTemplateForRequest.html.twig', $parameters);
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
    
    public function configUserAction() {
        $user = $this->getUser();
        if(!is_null($user)) {
            $parameters = array('user' => $user, 'selectedOption' => 'user');
            $template = 'MozcuMozcuBundle:Profile:_accountConfigUser.html.twig';
            if($this->getRequest()->isXmlHttpRequest()) {
                $parameters['parameters'] = $parameters;
                $parameters['template'] = $template;
                return $this->renderAjaxResponse('MozcuMozcuBundle:Profile:accountTemplateForAjax.html.twig', $parameters);
            }
            return $this->renderAccountForRequest($template, $parameters);
            
        } else {
            return new Response("Pagina no encontrada", 404);
        }
    }
    
    public function configProfileAction() {
        $user = $this->getUser();
        if(!is_null($user)) {
            $countries = $this->getRepository('MozcuMozcuBundle:Country')->findAll();
            $parameters = array('user' => $user, 'selectedOption' => 'profile', 'countries' => $countries);
            $template = 'MozcuMozcuBundle:Profile:_accountConfigProfile.html.twig';
            if($this->getRequest()->isXmlHttpRequest()) {
                $parameters['parameters'] = $parameters;
                $parameters['template'] = $template;
                return $this->renderAjaxResponse('MozcuMozcuBundle:Profile:accountTemplateForAjax.html.twig', $parameters);
            }
            return $this->renderAccountForRequest($template, $parameters);
            
        } else {
            return new Response("Pagina no encontrada", 404);
        }
    }
    
    public function accountConfigUserAction() {
        $user = $this->getUser();
        if(!is_null($user)) {
            $parameters = array('user' => $user, 'selectedOption' => 'user');
            $template = 'MozcuMozcuBundle:Profile:_accountConfigUser.html.twig';
            if($this->getRequest()->isXmlHttpRequest()) {
                return $this->renderAjaxResponse($template, $parameters);
            }
            return $this->renderAccountForRequest($template, $parameters);
            
        } else {
            return new Response("Pagina no encontrada", 404);
        }
    }
    
    public function accountConfigProfileAction() {
        $user = $this->getUser();
        if(!is_null($user)) {
            $countries = $this->getRepository('MozcuMozcuBundle:Country')->findAll();
            $parameters = array('user' => $user, 'selectedOption' => 'profile', 'countries' => $countries);
            $template = 'MozcuMozcuBundle:Profile:_accountConfigProfile.html.twig';
            if($this->getRequest()->isXmlHttpRequest()) {
                return $this->renderAjaxResponse($template, $parameters);
            }
            return $this->renderAccountForRequest($template, $parameters);
            
        } else {
            return new Response("Pagina no encontrada", 404);
        }
    }
    
    public function saveUserSettingsAction(Request $request) {
        $user = $this->getUser();
        if(!is_null($user)) {
            $data = $request->get('userData');
            
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $oldEncoded = $encoder->encodePassword($data['oldPassword'], $user->getSalt());
            if($user->getPassword() != $oldEncoded) {
                return $this->getJSONResponse(array('success' => false, 'message' => 'La contrase&ntilde;a actual es incorrecta', 'fields' => array('oldPassword')));
            }
            if($data['newPassword'] != $data['newPasswordConfirm']) {
                return $this->getJSONResponse(array('success' => false, 'message' => 'La nueva contrase&ntilde;a no coincide', 'fields' => array('newPasswordConfirm')));
            }
            if(!$this->validateEmail($data['email'])) {
                return $this->getJSONResponse(array('success' => false, 'message' => 'Email invalido', 'fields' => array('email')));
            }
            
            $this->getUserService()->updateUser($user, $data['email'], $data['newPassword']);
            return $this->getJSONResponse(array('success' => true, 'message' => 'La cuenta se actualizo correctamente'));
        
        } else {
            return new Response("Pagina no encontrada", 404);
        }
    }
    
    public function saveProfileSettingsAction(Request $request) {
        $user = $this->getUser();
        if(!is_null($user)) {
            $data = $request->get('profileData');
            
            if(empty($data['name']) || empty($data['country']) || empty($data['city']) || empty($data['paypalEmail'])) {
                return $this->getJSONResponse(array('success' => false, 'message' => 'Campos obligatorios incompletos', 'fields' => array('name', 'country', 'city', 'paypalEmail')));
            }
            if(!$this->validateEmail($data['paypalEmail'])) {
                return $this->getJSONResponse(array('success' => false, 'message' => 'Email invalido', 'fields' => array('paypalEmail')));
            }
            
            $country = $this->getRepository('MozcuMozcuBundle:Country')->find($data['country']);
            if(is_null($country)) {
                return $this->getJSONResponse(array('success' => false, 'message' => 'Pais invalido', 'fields' => array('country')));
            }
            $data['country'] = $country;
            
            $profile = $this->getProfileService()->updateProfile($user->getCurrentProfile(), $data);
            
            $response = array('success' => true, 'message' => 'El perfil se actualizo correctamente');
            if(isset($data['imageFileName'])) {
                $response['image'] = $profile->getProfileImageUrlForHeader();
            }
            
            return $this->getJSONResponse($response);
            
        } else {
            return new Response("Pagina no encontrada", 404);
        }
    }
    
    private function validateEmail($email) {
        $emailConstraint = new EmailConstraint();
        $errors = $this->get('validator')->validateValue(
            $email,
            $emailConstraint 
        );
        
        if (count($errors) > 0) {
            return false;    
        }
        return true;
    }
}
