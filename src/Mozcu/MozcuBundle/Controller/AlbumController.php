<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Mozcu\MozcuBundle\Exception\AppException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Mozcu\MozcuBundle\Entity\Album;


class AlbumController extends MozcuController
{
    
    private function renderTemplateForRequest($baseTemplate, $template, array $parametersForTemplate = array()) {
        $parameters['template'] = $template;
        $parameters['parameters'] = $parametersForTemplate;
        return $this->render($baseTemplate, $parameters);
    }
    
    private function renderAlbumsForRequest($template, array $parametersForTemplate = array()) {
        $base = 'MozcuMozcuBundle:Album:templateForRequest.html.twig';
        return $this->renderTemplateForRequest($base, $template, $parametersForTemplate);
    }
    
    private function renderAlbumForRequest($template, array $parametersForTemplate = array()) {
        $base = 'MozcuMozcuBundle:Album:albumTemplateForRequest.html.twig';
        return $this->renderTemplateForRequest($base, $template, $parametersForTemplate);
    }
    
    private function renderAlbumNotFound() {
        return $this->renderAlbumsForRequest('MozcuMozcuBundle:Album:_albumNotFound.html.twig', array());
    }
    
    public function loadAlbumHeaderAction(Album $album, $selected) 
    {
        $parameters['loggedInUser'] = $this->getUser();
        $parameters['album'] = $album;
        $parameters['selected'] = $selected;
        
        return $this->render('MozcuMozcuBundle:Album:_albumHeader.html.twig', $parameters);
    }
    
    /**
     * Pagina de albumes
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request) 
    {
        $parameters = array();
        if($request->get('tag')) {
            $tag = $this->getRepository('MozcuMozcuBundle:Tag')->findOneByName($request->get('tag'));
            if($tag) {
                $albums = $this->getRepository('MozcuMozcuBundle:Album')->findByTags(array($tag->getId()), 0, 16);
                $parameters['tag'] = $tag;
            } else {
                $albums = new \Doctrine\Common\Collections\ArrayCollection();    
            }
        } else {
            $albums = $this->getRepository('MozcuMozcuBundle:Album')->findAllPaginated(0, 16);    
        }
        $template = 'MozcuMozcuBundle:Album:_albumIndex.html.twig';
        $parameters['albums'] = $albums;
        $parameters['tags'] = $this->getRepository('MozcuMozcuBundle:Tag')->findMostPopular();
        
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->renderAjaxResponse($template, $parameters);
        } else {
           return $this->renderAlbumsForRequest($template, $parameters);
        }
    }
    
    /**
     * Carga una pagina de albums
     * 
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function nextPageAction($page) 
    {
        if($this->getRequest()->isXmlHttpRequest()) {
            $parameters = array();
            if($this->getRequest()->get('tag')) {
                $tag = $this->getRepository('MozcuMozcuBundle:Tag')->findOneByName($this->getRequest()->get('tag'));
                $albums = $this->getRepository('MozcuMozcuBundle:Album')->findByTags(array($tag->getId()), $page, 16);
                $parameters['tag'] = $tag;
            } else {
                $filters = $this->getRequest()->get('filters', []);
                $albums = $this->getRepository('MozcuMozcuBundle:Album')->findAllPaginated($page, 18, $filters);
            }
            $parameters['albums'] = $albums;
            $parameters['page']   = $page + 1;
            
            $template = 'MozcuMozcuBundle:Album:_albumsNextPage.html.twig';
            return $this->renderAjaxResponse($template, $parameters, true);
        } else {
            throw new BadRequestHttpException();
        }
    }
    
    public function loadFiltersAction()
    {
        $template = 'MozcuMozcuBundle:Album:_albumFilters.html.twig';
        $parameters['tags'] = $this->getRepository('MozcuMozcuBundle:Tag')->findMostPopular();
        
        return $this->render($template, $parameters);
    }
    
    /**
     * Pagina de album
     * 
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Mozcu\MozcuBundle\Controller\Exception
     */
    public function albumAction($username, $slug) 
    {
        try {
            $success = true;
            $album = $this->getRepository('MozcuMozcuBundle:Album')->findOneByUsernameAndSlug($username, $slug);
            if(!is_null($album)) {
                $template = "MozcuMozcuBundle:Album:_albumPlaylist.html.twig";
                $parameters = array('album' => $album, 'selected' => 'playlist');
            } else {
                if($response = $this->checkIdFromSlug($slug)) {
                    return $response;
                }
                $success = false;
            }
            
            if($this->getRequest()->isXmlHttpRequest()) {
                if($success) {
                    $parameters['parameters'] = $parameters;
                    $parameters['template'] = $template;
                    return $this->renderAjaxResponse('MozcuMozcuBundle:Album:albumTemplateForAjax.html.twig', $parameters);    
                } else {
                    return $this->getJSONResponse(array('success' => false));  
                }
            } else {
                if($success) {
                    return $this->renderAlbumForRequest($template, $parameters);   
                } else {
                    return $this->renderAlbumNotFound();
                }
            }  
        } catch(\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Verifica si se esta ingresando un id en lugar de un slug
     * 
     * @param string $slug
     */
    private function checkIdFromSlug($slug)
    {
        $album = $this->getRepository('MozcuMozcuBundle:Album')->find($slug);
        if(!is_null($album)) {
            return $this->redirect($this->generateUrl('MozcuMozcuBundle_albumAlbum', ['username' => $album->getProfile()->getUsername(),
                                                                                      'slug' => $album->getSlug()]));
        }
        return false;
    }
    
    /**
     * 
     * @param type $username
     * @param type $slug
     * @param type $action
     * @return Response
     */
    public function albumOptionAction($username, $slug, $action)
    {   
        if($this->getRequest()->isXmlHttpRequest()) {
            $album = $this->getRepository('MozcuMozcuBundle:Album')->findOneByUsernameAndSlug($username, $slug);
            if(is_null($album)) {
                return $this->getJSONResponse(['success' => false, 'message' => 'album not found']);
            }

            $template = 'MozcuMozcuBundle:Album:_album' . ucfirst($action) . '.html.twig';
            $parameters = ['album' => $album, 'selected' => $action];
            if ($action == 'related') {
                $parameters['albums'] = $this->getRepository('MozcuMozcuBundle:Album')->findRelated($album);
            }
            return $this->renderAjaxResponse($template, $parameters);
        } else {
            throw new BadRequestHttpException();
        }  
    }
    
    /**
     * 
     * @param integer $id
     * @return string
     * @throws BadRequestHttpException
     */
    public function albumReportModalAction($id) {
        if($this->getRequest()->isXmlHttpRequest()) {
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                $template = "MozcuMozcuBundle:Album:_albumReportModal.html.twig";
                $parameters = array('album' => $album);
                return $this->renderAjaxResponse($template, $parameters);
            } else {
                return $this->getJSONResponse(array('success' => false));
            }
        } else {
            throw new BadRequestHttpException();
        }  
    }
    
    /**
     * TODO: definir funcionalidad
     * 
     * @param integer $id
     * @return string
     * @throws BadRequestHttpException
     */
    public function albumSubmitReportAction($id) {
        if($this->getRequest()->isXmlHttpRequest()) {
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                return $this->getJSONResponse(array('success' => true));
            } else {
                return $this->getJSONResponse(array('success' => false));
            }
        } else {
            throw new BadRequestHttpException();
        }
    }
    
    public function albumShareModalAction(Album $album) {
        if($this->getRequest()->isXmlHttpRequest()) {
            $template = "MozcuMozcuBundle:Album:_albumShareModal.html.twig";
            $parameters = array('album' => $album);
            return $this->renderAjaxResponse($template, $parameters);
        } else {
            throw new BadRequestHttpException();
        }  
    }
    
    /**
     * Retorna la informacion del modal de descarga de album
     * 
     * @param integer $id
     * @return string
     * @throws BadRequestHttpException
     */
    public function albumDownloadModalAction($id) {
        if($this->getRequest()->isXmlHttpRequest()) {
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                return $this->renderAjaxResponse('MozcuMozcuBundle:Album:_albumDownloadModal.html.twig', ['album' => $album]);
            } else {
                return $this->getJSONResponse(array('success' => false));
            }
        } else {
            throw new BadRequestHttpException();
        }  
    }
    
    /**
     * Devuelve la url de descarga del disco
     * 
     * @param integer $id
     * @return string
     * @throws BadRequestHttpException
     */
    public function albumCheckoutAction($id, $increaseCounter) {
        if($this->getRequest()->isXmlHttpRequest()) {
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(is_null($album)) {
                return $this->getJSONResponse(array('success' => false));
            }
            
            if(is_null($album->getZipUrl())) {
                $this->getAlbumService()->prepareZip($album);
            }
            
            // Incrementamos el contador de descargas
            if ($increaseCounter) {
                $this->getAlbumService()->increaseDownloadCount($album);
            }
            
            return $this->getJSONResponse(array('success' => true, 'zipUrl' => $album->getZipUrl()));    
        } else {
            throw new BadRequestHttpException();
        }
    }
    
    
    public function checkOutFromPaymentAction($checkoutId)
    {
        if (!$this->getRequest()->isXmlHttpRequest() && !is_null($this->getPaymentService()->getCheckout($checkoutId))) {
            $checkout = $this->getPaymentService()->getCheckout($checkoutId);
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($checkout['album_id']);
            if (!is_null($album)) {
                return $this->render('MozcuMozcuBundle:Album:albumDownloadFromCheckout.html.twig', ['album' => $album]);
            } 
        }
        throw new BadRequestHttpException();
    }
    
    public function getTagsLiveAction(Request $request) {
        $tags = $this->getRepository('MozcuMozcuBundle:Tag')->liveSearchByName($request->get('term'), true);
        
        $result = array();
        foreach($tags as $tag) {
            $result[] = array('id' => $tag->getId(), 'label' => $tag->getName(), 'value' => $tag->getName());
        }
        return $this->getJSONResponse($result);
    }
    
    public function findAlbumsByTagsAction(Request $request) {
        try {
            $tags = $request->get('tags');
            $albums = $this->getRepository('MozcuMozcuBundle:Album')->findByTags($tags);
            $html = array();
            foreach($albums as $album) {
                $html[] = $this->renderView('MozcuMozcuBundle:Album:_album.html.twig', array('album' => $album));
            }
            $result = array('success' => true, 'html' => $html);
        } catch(\Exception $e) {
            $result = array("success" => false, "message" => $e->getMessage());    
        }
        
        return $this->getJSONResponse($result);
    }
    
    public function getAlbumForPlayerAction(Request $request) {
        try {
            $id = $request->get('id');
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                $albumData['id'] = $album->getId();
                $albumData['name'] = $album->getName();
                $albumData['image'] = $album->getCoverImageUrl();
                $artist = $album->getArtistName();
                $albumData['songs'] = array();
                foreach($album->getSongs() as $song) {
                    $songData['title'] = $song->getName();
                    $songData['mp3'] = $song->getUrl();
                    $songData['artist'] = $artist;
                    $albumData['songs'][] = $songData;
                }
                // Incrementamos el contador de reproducciones
                $this->getAlbumService()->increasePlayCount($album);
                
                return $this->getJSONResponse(array('success' => true, 'album' => $albumData));
            } else {
                return $this->getJSONResponse(array('success' => false, 'message' => 'El album no existe'));   
            }
        } catch(\Exception $e) {
            return $this->getJSONResponse(array('success' => false, 'message' => $e->getMessage()));   
        }
    }
    
    public function deleteAlbumAction(Request $request) {
        try {
            if($this->getRequest()->isXmlHttpRequest()) {
                $id = $request->get('id');
                $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
                if(!is_null($album)) {
                    $this->getAlbumService()->deleteAlbum($album);
                    return $this->getJSONResponse(array('success' => true));
                } else {
                    throw new AppException('The album does not exist');
                }
            } else {
                throw new AppException('Invalid request');
            }
        } catch(\Exception $e) {
            return $this->getJSONResponse(array('success' => false, 'message' => $e->getMessage()));   
        }
    }
    
    public function editAlbumAction($id) {
        $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
        if(!is_null($album)) {
            $parameters = array('album' => $album, 'username' => $this->getUser()->getUsername());
            $template = "MozcuMozcuBundle:UploadAlbum:indexAjax.html.twig";
            if($this->getRequest()->isXmlHttpRequest()) {
                return $this->renderAjaxResponse($template, $parameters);
            }
            return $this->render($template, $parameters);

        } else {
            return $this->getJSONResponse(array('success' => false));
        }
    }
    
    public function updateAlbumAction($id, Request $request) {
        try {
            $data = $request->get('album');
            
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            $album = $this->getAlbumService()->updateAlbum($album, $data, true);
            $content =array('success' => true,
                            'album_id' => $album->getId(),
                            'album_name' => $album->getName());
        } catch (\Exception $e) {
            $content = array('success' => false, 'message' => $e->getMessage());
        }
        
        return $this->getJSONResponse($content);
    }
}
