<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class ActionLogController extends MozcuController
{
    /**
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function logAlbumAction(Request $request) 
    {
        if(!$this->getRequest()->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = $this->prepareData($request->request->all());
        try {
            $this->getActionLogService()->logAlbum($data);
            return $this->getJSONResponse(['success' => true]);
        } catch (\Exception $e) {
            return $this->getJSONResponse(['success' => false]);
        }
    }
    
    /**
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function logSongAction(Request $request) 
    {
        if(!$this->getRequest()->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = $this->prepareData($request->request->all());
        try {
            $this->getActionLogService()->logSong($data);
            return $this->getJSONResponse(['success' => true]);
        } catch (\Exception $e) {
            return $this->getJSONResponse(['success' => false]);
        }
    }
    
    /**
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestHttpException
     */
    public function logAlbumDownloadAction(Request $request) 
    {
        if(!$this->getRequest()->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        
        $data = $this->prepareData($request->request->all());
        try {
            $this->getActionLogService()->logDownload($data);
            return $this->getJSONResponse(['success' => true]);
        } catch (\Exception $e) {
            return $this->getJSONResponse(['success' => false]);
        }
    }
    
    private function prepareData(array $data)
    {
        if ($this->getUser()) {
            $data['user_id'] = $this->getUser()->getId();
        }
        
        $data['ip_address'] = $this->getRequest()->getClientIp();
        //$data['country'];
        
        return $data;
    }
}
