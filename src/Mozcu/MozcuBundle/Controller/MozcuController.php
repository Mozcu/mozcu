<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

abstract class MozcuController extends Controller{
    
    /**
     * 
     * @return \Mozcu\MozcuBundle\Service\UserService
     */
    protected function getUserService() {
        return $this->get('mozcu_mozcu.user_service');
    }
    
    /**
     * 
     * @return \Mozcu\MozcuBundle\Service\ProfileService
     */
    protected function getProfileService() {
        return $this->get('mozcu_mozcu.profile_service');
    }
    
    /**
     * 
     * @return \Mozcu\MozcuBundle\Service\AlbumService
     */
    protected function getAlbumService() {
        return $this->get('mozcu_mozcu.album_service');
    }
    
    /**
     * 
     * @return \Mozcu\MozcuBundle\Service\UploadService
     */
    protected function getUploadService() {
        return $this->get('mozcu_mozcu.upload_service');
    }
    
    /**
     * 
     * @return \Mozcu\MozcuBundle\Service\EmailService
     */
    public function getEmailService() {
        return $this->get('mozcu_mozcu.email_service');
    }
    
    /**
     * 
     * @param string $className
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($className) {
        return $this->getDoctrine()->getEntityManager()->getRepository($className);
    }
    
    /**
     * 
     * @param array $content
     * @return \Mozcu\MozcuBundle\Controller\Response
     */
    protected function getJSONResponse(array $content = array()) {
        $response = new Response(json_encode($content));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * 
     * @param string $template
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderAjaxResponse($template, array $parameters = array()) {
        $html = $this->renderView($template, $parameters);
        $response = new Response(json_encode(array('success' => true, 'html' => $html)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    protected function debug($object) {
        echo '<pre>';
        var_dump($object);
        echo '</pre>';
        die;
    }
}

?>
