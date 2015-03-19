<?php

namespace Mozcu\MozcuBundle\Controller;

use Mozcu\MozcuBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HomeController extends MozcuController
{
    
    /**
     * 
     * @param string $template
     * @param arrray $parameters
     * @param array $parametersForTemplate
     * @return Response
     */
    private function renderTemplateForRequest($template, array $parameters) {
        $params['template'] = $template;
        $params['parameters'] = $parameters;
        return $this->render('MozcuMozcuBundle:Home:templateForRequest.html.twig', $params);
    }
    
    /**
     * 
     * @return Response
     */
    public function indexAction() {
        $user = $this->getUser();
        if(!is_null($user)) {
            return $this->redirect($this->generateUrl('MozcuMozcuBundle_profile', array('username' => $user->getUsername())));
        }
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('MozcuMozcuBundle_ajaxGetHome'));
        }
        
        return $this->render( 'MozcuMozcuBundle:Home:index.html.twig');
    }
    
    public function loginAction() {
        $user = $this->getUser();
        if(!is_null($user)) {
            return $this->redirect($this->generateUrl('MozcuMozcuBundle_profile', array('username' => $user->getUsername())));
        }
        
        $template = 'MozcuMozcuBundle:Home:_homeLogin.html.twig';
        $parameters = array();
        if($this->getRequest()->isXmlHttpRequest($template)) {
            $html = $this->renderView($template);
            $response = new Response(json_encode(array('success' => true, 'html' => $html)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return $this->render( 'MozcuMozcuBundle:Home:templateForRequest.html.twig', array('parameters' => $parameters, 'template' => $template));
        }
    }
    
    public function oldLoginCheckAction(Request $request) {
        if(!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException;
        }
        
        $email = $request->get('_username');
        $password = $request->get('_password');
        
        if(empty($email) || empty($password)) {
            throw new BadRequestHttpException;
        }
        
        $user = $this->getRepository('MozcuMozcuBundle:User')->findOneBy(array('email' => $email));
        if(is_null($user)) {
            $response = array('success' => false, 'message' => "User doesn't exists");
        } else {
            $response = array(
                'success' => true,
                'login_check' => $this->getUserService()->oldLoginCheck($user, $password)
            );

            if($response['login_check']) {
                $response['callback_url'] = $this->generateUrl('MozcuMozcuBundle_profile', array('username' => $user->getUsername()));
            }
        }
        
        return $this->getJSONResponse($response);
    }
    
    public function ajaxGetHomeAction() {
        if($this->getRequest()->isXmlHttpRequest()) {
            $html = $this->renderView('MozcuMozcuBundle:Home:_homeHome.html.twig');
            $response = new Response(json_encode(array('success' => true, 'html' => $html)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            throw $this->createNotFoundException('Not found');
        }
    }
    
    public function getRegistrationAction(Request $request) {
        $template = 'MozcuMozcuBundle:Home:_registrationForm.html.twig';
        $countries = $this->getRepository('MozcuMozcuBundle:Country')->findAll();
        
        if($request->isXmlHttpRequest()) {
            $html = $this->renderView($template, ['countries' => $countries]);
            return $this->getJSONResponse(['success' => true, 'html' => $html]);
        }
        return $this->render( 'MozcuMozcuBundle:Home:templateForRequest.html.twig', array('parameters' => ['countries' => $countries], 'template' => $template));
    }
    
    public function postRegistrationAction(Request $request) {
        if($request->isXmlHttpRequest()) {
            $data = $request->get('data');
            
            try {
                $validation = $this->getUserService()->validateAccountData($data);
                if($validation['success']) {
                    $user = $this->getUserService()->createUser($data);
                    $this->getUserService()->logUser($user);
                    $validation['callback_url'] = $this->generateUrl('MozcuMozcuBundle_profile', 
                                                                      array('username' => $user->getUsername()));
                }
                return $this->getJSONResponse($validation);
            } catch(\Exception $e) {
                return $this->getJSONResponse(array('success' => false, 'message' => $e->getMessage()));
            }
        } else {
            throw new BadRequestHttpException();
        }
    }
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadUserBarAction() {
        $user = $this->getUser();
        
        if($this->getRequest()->isXmlHttpRequest()) {
            $html = $this->renderView('MozcuMozcuBundle:Home:_userBar.html.twig', array('user' => $user));
            $response = new Response(json_encode(array('success' => true, 'html' => $html)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
        return $this->render('MozcuMozcuBundle:Home:_userBar.html.twig', array('user' => $user));
    }
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadLeftBarAction() {
        $user = $this->getUser();
        
        if($this->getRequest()->isXmlHttpRequest()) {
            $html = $this->renderView('MozcuMozcuBundle:Home:_leftBarMenu.html.twig', array('user' => $user));
            $response = new Response(json_encode(array('success' => true, 'html' => $html)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
        return $this->render('MozcuMozcuBundle:Home:_leftBar.html.twig', array('user' => $user));
    }
    
    public function ajaxLiveSearchAction() {
        if($this->getRequest()->isXmlHttpRequest()) {
            $query = $this->getRequest()->get('query');
            $parameters['profiles'] = $this->getRepository('MozcuMozcuBundle:Profile')->liveSearch($query);
            $parameters['albums'] = $this->getRepository('MozcuMozcuBundle:Album')->liveSearch($query);
            $parameters['songs'] = $this->getRepository('MozcuMozcuBundle:Song')->liveSearch($query);
            $parameters['query'] = $query;
            
            $template = "MozcuMozcuBundle:Home:_liveSearchResult.html.twig";
            
            return $this->renderAjaxResponse($template, $parameters);
        } else {
            throw new BadRequestHttpException();
        }
    }
    
    public function searchAction($query) {
        if(is_null($query)) {
            throw new BadRequestHttpException();
        }
        $parameters['profiles'] = $this->getRepository('MozcuMozcuBundle:Profile')->liveSearch($query, 6);
        $parameters['totalProfiles'] = $this->getRepository('MozcuMozcuBundle:Profile')->searchTotalCount($query);
        $parameters['albums'] = $this->getRepository('MozcuMozcuBundle:Album')->liveSearch($query, 6);
        $parameters['totalAlbums'] = $this->getRepository('MozcuMozcuBundle:Album')->searchTotalCount($query);
        $parameters['songs'] = $this->getRepository('MozcuMozcuBundle:Song')->liveSearch($query, 15);
        $parameters['totalSongs'] = $this->getRepository('MozcuMozcuBundle:Song')->searchTotalCount($query);
        $parameters['query'] = $query;
        
        $template = "MozcuMozcuBundle:Home:_searchResult.html.twig";
        
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->renderAjaxResponse($template, $parameters);
        } else {
            return $this->renderTemplateForRequest($template, $parameters);
        }
    }
    
    public function aboutAction() {
        $template = 'MozcuMozcuBundle:Home:_homeAbout.html.twig';
        $parameters = array();
        if($this->getRequest()->isXmlHttpRequest($template)) {
            $html = $this->renderView($template);
            $response = new Response(json_encode(array('success' => true, 'html' => $html)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return $this->render( 'MozcuMozcuBundle:Home:templateForRequest.html.twig', array('parameters' => $parameters, 'template' => $template));
        }
    }
    
    public function termsAction() {
        $template = 'MozcuMozcuBundle:Home:_homeTerms.html.twig';
        $parameters = array();
        if($this->getRequest()->isXmlHttpRequest($template)) {
            $html = $this->renderView($template);
            $response = new Response(json_encode(array('success' => true, 'html' => $html)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return $this->render( 'MozcuMozcuBundle:Home:templateForRequest.html.twig', array('parameters' => $parameters, 'template' => $template));
        }
    }
}
