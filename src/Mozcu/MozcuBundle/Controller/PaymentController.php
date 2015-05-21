<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Mozcu\MozcuBundle\Exception\ServiceException;
use Mozcu\MozcuBundle\Entity\Album;


class PaymentController extends MozcuController
{
    
    public function renderPaypalFormAction(Album $album) {
        $checkoutId = $this->getPaymentService()->createCheckout($album);
        return $this->render('MozcuMozcuBundle:Payment:paypalForm.html.twig', ['album' => $album, 'checkoutId' => $checkoutId]);
    }
    
    public function ajaxSetCheckoutPriceAction($checkoutId)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            try {
                $this->getPaymentService()->updateCheckout($checkoutId, $this->getRequest()->get('price'));
                return $this->getJSONResponse(['success' => true]);
            } catch (ServiceException $e) {
                return $this->getJSONResponse(['success' => true]);
            }
        }
        //throw new BadRequestHttpException();
    }


    public function returnFromPaymentAction($service, $checkoutId)
    {
        if (!is_null($this->getPaymentService()->getCheckout($checkoutId))) {
            $checkout = $this->getPaymentService()->getCheckout($checkoutId);
            $album = $this->getRepository('MozcuMozcuBundle:Album')->find($checkout['album_id']);
            if(!is_null($album)) {
                $user = $this->getUser();
                try {
                    $this->getPaymentService()->createPurchase($album, $service, $checkout['price'], [], $user);
                    return $this->redirect($this->generateUrl('MozcuMozcuBundle_albumCheckoutFromPaymentAction', ['checkoutId' => $checkoutId]));
                } catch (ServiceException $e) {
                    throw new BadRequestHttpException();
                }                
            }
        }
        throw new BadRequestHttpException();
    }
}
