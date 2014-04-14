<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Mozcu\MozcuBundle\Exception\AppException;


class AlbumController extends MozcuController
{
    
    /**
     *
     * @param string $baseTemplate
     * @param string $template
     * @param arrray $parameters
     * @param array $parametersForTemplate
     * @return Response
     */
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
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request) {
        $parameters = array();
        if($request->get('tag')) {
            $tag = $this->getRepository('MozcuMozcuBundle:Tag')->findOneByName($request->get('tag'));
            if($tag) {
                $albums = $this->getRepository('MozcuMozcuBundle:Album')->findByTags(array($tag->getId()));
                $parameters['tag'] = $tag;
            } else {
                $albums = new \Doctrine\Common\Collections\ArrayCollection();    
            }
        } else {
            $albums = $this->getRepository('MozcuMozcuBundle:Album')->findAllPaginated(1, 18);    
        }
        $tags = $this->getRepository('MozcuMozcuBundle:Tag')->getTagsPaginated(1, 10);
        
        $template = 'MozcuMozcuBundle:Album:_albumIndex.html.twig';
        $parameters['albums'] = $albums;
        $parameters['tags'] = $tags;
        
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->renderAjaxResponse($template, $parameters);
        } else {
           return $this->renderAlbumsForRequest($template, $parameters);
        }
    }
    
    public function albumAction($id) {
        try {
            $success = true;
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                $template = "MozcuMozcuBundle:Album:_albumPlaylist.html.twig";
                $parameters = array('album' => $album, 'selected' => 'playlist');
            } else {
                $success = false;   
            }
            
            if($this->getRequest()->isXmlHttpRequest()) {
                if($success) {
                    $parameters['parameters'] = $parameters;
                    $parameters['template'] = $template;
                    return $this->renderAjaxResponse('MozcuMozcuBundle:Album:albumTemplateForAjax.html.twig', $parameters);    
                } else {
                    return $this->getJSONResponse(array('success' => 'false'));  
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
    
    public function albumPlaylistAction($id) {
        try {
            $success = true;
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                $template = "MozcuMozcuBundle:Album:_albumPlaylist.html.twig";
                $parameters = array('album' => $album, 'selected' => 'information');
            } else {
                $success = false;
            }
            
            if($this->getRequest()->isXmlHttpRequest()) {
                if($success) {
                    return $this->renderAjaxResponse($template, $parameters);    
                } else {
                    return $this->getJSONResponse(array('success' => 'false'));   
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

    public function albumsRelatedAction($id) {
        try {
            $success = true;
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            
            if(!is_null($album)) {
                $albums = $this->getRepository('MozcuMozcuBundle:Album')->findRelated($album);
                $template = "MozcuMozcuBundle:Album:_albumRelated.html.twig";
                $parameters = array('albums' => $albums, 'selected' => 'related');
            } else {
                $success = false;
            }
            
            if($this->getRequest()->isXmlHttpRequest()) {
                if($success) {
                    return $this->renderAjaxResponse($template, $parameters);    
                } else {
                    return $this->getJSONResponse(array('success' => 'false'));   
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
    
    
    public function albumInformationAction($id) {
        try {
            $success = true;
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                $template = "MozcuMozcuBundle:Album:_albumInformation.html.twig";
                $parameters = array('album' => $album, 'selected' => 'information');
            } else {
                $success = false;
            }
            
            if($this->getRequest()->isXmlHttpRequest()) {
                if($success) {
                    return $this->renderAjaxResponse($template, $parameters);    
                } else {
                    return $this->getJSONResponse(array('success' => 'false'));   
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
    
    public function albumPreDownloadAction($id) {
        try {
            $success = true;
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                $template = "MozcuMozcuBundle:Album:_albumDownload.html.twig";
                $parameters = array('album' => $album, 'selected' => 'download');
            } else {
                $success = false;
            }
            
            if($this->getRequest()->isXmlHttpRequest()) {
                if($success) {
                    return $this->renderAjaxResponse($template, $parameters);    
                } else {
                    return $this->getJSONResponse(array('success' => 'false'));   
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
    
    public function albumCheckoutAction($id) {
        try {
            $success = true;
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                $template = "MozcuMozcuBundle:Album:_albumCheckout.html.twig";
                $parameters = array('album' => $album, 'selected' => 'download');
            } else {
                $success = false;   
            }
            
            if($this->getRequest()->isXmlHttpRequest()) {
                if($success) {
                    $parameters['parameters'] = $parameters;
                    $parameters['template'] = $template;
                    return $this->renderAjaxResponse('MozcuMozcuBundle:Album:albumTemplateForAjax.html.twig', $parameters);    
                } else {
                    return $this->getJSONResponse(array('success' => 'false'));  
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
    
    public function albumDownloadAction($id) {
        try {
            $success = true;
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
            if(!is_null($album)) {
                if(is_null($album->getZipUrl())) {
                    $zipUrl = $this->getMusicService()->createAlbumZip($album);
                } else {
                    $zipUrl = $album->getZipUrl();
                }
               
            } else {
                $success = false;
            }
            
            if($this->getRequest()->isXmlHttpRequest()) {
                if($success) {
                    return $this->getJSONResponse(array('success' => true, 'zipUrl' => $zipUrl));    
                } else {
                    return $this->getJSONResponse(array('success' => 'false'));   
                }
            } else {
                return $this->renderAlbumNotFound();
            }
        } catch(\Exception $e) {
            throw $e;
        }
    }
    
    public function getTagsPaginatedAction($page) {
        try {
            $limit = 10;
            $tags = $this->getRepository('MozcuMozcuBundle:Tag')->getTagsPaginated($page, $limit);
            $result = array("success" => true, "tags" => $tags);    
        } catch(\Exception $e) {
            $result = array("success" => false, "message" => $e->getMessage());    
        }
        
        return $this->getJSONResponse($result);
        
    }
    
    public function getTagsLiveAction(Request $request) {
        $tags = $this->getRepository('MozcuMozcuBundle:Tag')->liveSearchByName($request->get('term'));
        
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
                $artist = $album->getProfile()->getUser()->getCurrentName();
                $albumData['songs'] = array();
                foreach($album->getSongs() as $song) {
                    $songData['title'] = $song->getName();
                    $songData['mp3'] = $song->getUrl();
                    $songData['artist'] = $artist;
                    $albumData['songs'][] = $songData;
                }
                return $this->getJSONResponse(array('success' => true, 'album' => $albumData));
            } else {
                return $this->getJSONResponse(array('success' => 'false', 'message' => 'El album no existe'));   
            }
        } catch(\Exception $e) {
            return $this->getJSONResponse(array('success' => 'false', 'message' => $e->getMessage()));   
        }
    }
    
    public function deleteAlbumAction(Request $request) {
        try {
            if($this->getRequest()->isXmlHttpRequest()) {
                $id = $request->get('id');
                $album = $this->getRepository('MozcuMozcuBundle:Album')->find($id);
                if(!is_null($album)) {
                    $this->getMusicService()->deleteAlbum($album);
                    return $this->getJSONResponse(array('success' => true));
                } else {
                    throw new AppException('The album does not exist');
                }
            } else {
                throw new AppException('Invalid request');
            }
        } catch(\Exception $e) {
            return $this->getJSONResponse(array('success' => 'false', 'message' => $e->getMessage()));   
        }
    }
}
