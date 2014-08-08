<?php

namespace Mozcu\MozcuBundle\Controller;

use Mozcu\MozcuBundle\Form\Type\UserType;
use Mozcu\MozcuBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends MozcuController
{
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
    
    public function ajaxLiveSearchAction(Request $request) {
        $term = $request->get('term');
        
        $profiles = $this->getRepository('MozcuMozcuBundle:Profile')->liveSearchByName($term);
        $albums = $this->getRepository('MozcuMozcuBundle:Album')->liveSearchByName($term);
        $tags = $this->getRepository('MozcuMozcuBundle:Tag')->liveSearchByName($term);
        
        $result = $this->prepareLiveSearchData($profiles, $albums, $tags);
        return $this->getJSONResponse($result);
    }
    
    private function prepareLiveSearchData($profiles, $albums, $tags) {
        $result = array();
        
        /* Profiles */
        foreach($profiles as $profile) {
            $data = array();
            $image = $profile->getMainImage();
            if(!is_null($image)) {
                foreach($image->getPresentations() as $presentation) {
                    if($presentation->getName() == 'livesearch') {
                        $data['image'] = $presentation->getUrl();
                    }
                }
            } else {
                $image = $this->container->getParameter('default_profile_image_live');
                $data['image'] = $this->container->get('templating.helper.assets')->getUrl($image);
            }
            $data['id'] = $profile->getUser()->getUsername();
            $data['label'] = $profile->getUser()->getCurrentName();
            $data['value'] = $profile->getUser()->getCurrentName();
            $data['type'] = "Artista";
            $data['type_id'] = '1';
            $data['url'] = $this->get('router')->generate("MozcuMozcuBundle_profile", array('username' => $profile->getUser()->getUsername()));
            $result[] = $data;
        }
        
        /* Albums */
        foreach($albums as $album) {
            $data = array();
            $image = $album->getImage();
            if(!is_null($image)) {
                foreach($image->getPresentations() as $presentation) {
                    if($presentation->getName() == 'livesearch') {
                        $data['image'] = $presentation->getUrl();
                    }
                }
            } else {
                $image = $this->container->getParameter('default_profile_image_live');
                $data['image'] = $this->container->get('templating.helper.assets')->getUrl($image);
            }
            $data['id'] = $album->getId();
            $data['label'] = $album->getName();
            $data['value'] = $album->getName();
            $data['type'] = "Album";
            $data['type_id'] = '2';
            $data['extra'] = $album->getProfile()->getName();
            $data['url'] = $this->get('router')->generate("MozcuMozcuBundle_albumAlbum", array('id' => $album->getId(), 
                                                          'username' => $album->getProfile()->getUser()->getUsername()));
            $result[] = $data;
        }
        
        /* Tags */
        foreach($tags as $tag) {
            $data['id'] = $tag->getId();
            $data['label'] = $tag->getName();
            $data['value'] = $tag->getName();
            $data['type'] = "Etiqueta";
            $data['type_id'] = '3';
            $data['url'] = $this->get('router')->generate("MozcuMozcuBundle_albumsTag", array('tag' => $tag->getName()));
            $result[] = $data;
        }
        
        return $result;
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
