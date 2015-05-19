<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Mozcu\MozcuBundle\Exception\ServiceException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Mozcu\MozcuBundle\Entity\Album;

class PaymentController extends MozcuController
{
    
    public function returnFromPaymentAction($service, $checkoutId)
    {
        if ($this->getRequest()->getSession()->has($checkoutId)) {
            $albumId = $this->getRequest()->getSession()->get($checkoutId);
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($albumId);
            if(!is_null($album)) {
                $user = $this->getUser();
                $this->getPaymentService()->createPurchase($album, $service, '0', [], $user);
                return $this->redirect($this->generateUrl('MozcuMozcuBundle_albumCheckoutFromPaymentAction', ['checkoutId' => $checkoutId]));
            }
            
        }
        throw new BadRequestHttpException();
    }
    
    public function notifyPaypalAction(Request $request)
    {
        
    }
    
}
