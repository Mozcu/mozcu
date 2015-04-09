<?php
/**
 * Description of UploadAlbumController
 *
 * @author mauro
 */

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Mozcu\MozcuBundle\Entity\Album;
use Mozcu\MozcuBundle\Form\Type\AlbumType;

class UploadAlbumController extends MozcuController {
    
    public function indexAction() {
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->renderAjaxResponse('MozcuMozcuBundle:UploadAlbum:indexAjax.html.twig', 
                                             array('username' => $this->getUser()->getUsername()));
        }
        return $this->render('MozcuMozcuBundle:UploadAlbum:index.html.twig', 
                             array('username' => $this->getUser()->getUsername()));
    }
    
    public function uploadSongAction(Request $request) {
        try {
            $file = $request->files->get('Filedata');
            $fileName = $this->getUploadService()->processTemporarySong($file);
            $originalName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->getClientOriginalName());
            $content = array('success' => true, 'file_name' => $fileName, 'original_name' => $originalName);
        } catch (\Exception $e) {
            $content = array('success' => false, 'message' => $e->getMessage());
        }
        
        return $this->getJSONResponse($content);
    }
    
    public function uploadImageAction(Request $request) {
        try {
            $file = $request->files->get('image');
            $fileName = $this->getUploadService()->processTemporaryImage($file[0]);
            $content = array('success' => true, 'file_name' => $fileName);
        } catch (\Exception $e) {
            $content = array('success' => false, 'message' => $e->getMessage());
        }
        
        return $this->getJSONResponse($content);
    }
    
    public function getUploadedSongTemplateAction() {
        $html = $this->renderView('MozcuMozcuBundle:UploadAlbum:_uploadedSong.html.twig');
        return $this->getJSONResponse(array('success' => true, 'html' => $html));
    }
    
    public function getTagsAction(Request $request) {
        $tags = $this->getRepository('MozcuMozcuBundle:Tag')->liveSearchByName($request->get('term'));
        
        $result = array();
        foreach($tags as $tag) {
            $result[] = array('id' => $tag->getId(), 'label' => $tag->getName(), 'value' => $tag->getName());
        }
        return $this->getJSONResponse($result);
    }
    
    public function uploadAlbumAction(Request $request) {
        try {
            $albumData = $request->get('album');
            $album = $this->getAlbumService()->createAlbum($this->getUser()->getCurrentProfile(), $albumData);
            $content = array('success' => true,
                            'album_id' => $album->getId(),
                            'album_name' => $album->getName());
        } catch (\Exception $e) {
            $content = array('success' => false, 'message' => $e->getMessage());
        }
        
        return $this->getJSONResponse($content);
    }
}