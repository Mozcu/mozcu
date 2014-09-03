<?php

namespace Mozcu\MozcuBundle\Controller;

use Mozcu\MozcuBundle\Form\Type\UserType;
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
        
        $form = $this->createForm(new UserType(), new User());
        return $this->render( 'MozcuMozcuBundle:Home:index.html.twig', array('form' => $form->createView()));
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
    
    public function ajaxGetHomeAction() {
        $form = $this->createForm(new UserType(), new User());
        
        if($this->getRequest()->isXmlHttpRequest()) {
            $html = $this->renderView('MozcuMozcuBundle:Home:_homeHome.html.twig', array('form' => $form->createView()));
            $response = new Response(json_encode(array('success' => true, 'html' => $html)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            throw $this->createNotFoundException('Not found');
        }
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response
     */
    public function registerAction(Request $request) {
        $form = $this->createForm(new UserType(), new User());
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $user = $form->getData();
            $response = $this->getUserService()->checkUserDisponibility($user->getUsername(), $user->getEmail());
            if($response['available']) {
                try {
                    $user = $this->getUserService()->createUser($user->getUsername(), $user->getPassword(), $user->getEmail());
                    $this->getUserService()->logUser($user);
                    $url = $this->generateUrl('MozcuMozcuBundle_profile');
                    return $this->redirect($url);
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        $e->getMessage()
                    );
                }
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $response['message']
                );
            }
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Verifique por favor los datos ingresados'
            );
        }
        
        $url = $this->generateUrl('MozcuMozcuBundle_home');
        return $this->redirect($url);
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
