<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;

use Mozcu\MozcuBundle\Exception\ServiceException;
use Mozcu\MozcuBundle\Entity\Album,
    Mozcu\MozcuBundle\Entity\Profile,
    Mozcu\MozcuBundle\Entity\PaymentMethod;


class PaymentController extends MozcuController
{
    
    public function renderPaypalFormAction(Album $album) {
        $checkoutId = $this->getPaymentService()->createCheckout($album);
        return $this->render('MozcuMozcuBundle:Payment:paypalForm.html.twig', ['album' => $album, 'checkoutId' => $checkoutId]);
    }
    
    public function renderMercadopagoInputAction(Album $album)
    {
        $checkoutId = $this->getPaymentService()->createCheckout($album);
        return $this->render('MozcuMozcuBundle:Payment:mercadopagoInput.html.twig', ['album' => $album, 'checkoutId' => $checkoutId]);
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
        throw new BadRequestHttpException();
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
                    $this->getAlbumService()->increaseDownloadCount($album);
                    return $this->redirect($this->generateUrl('MozcuMozcuBundle_albumCheckoutFromPaymentAction', ['checkoutId' => $checkoutId]));
                } catch (ServiceException $e) {
                    throw new BadRequestHttpException();
                }                
            }
        }
        throw new BadRequestHttpException();
    }
    
    public function returnFromMercadopagoAuthAction(Profile $profile)
    {
        $this->getPaymentService()->createPaymentMethod($profile, PaymentMethod::MERCADOPAGO, 
                                                              ['code' => $this->getRequest()->get('code')]);
        $this->getRequest()->getSession()->getFlashBag()->add(
            'notice',
            'Tu cuenta de Mercado Pago se ha vinculado correctamente!'
        );
        
        return $this->redirect($this->generateUrl('MozcuMozcuBundle_account'));
        
    }
    
    public function unlinkMercadopagoAction(Request $request) 
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        $pm = $this->getUser()->getProfile()->getPaymentMethod(PaymentMethod::MERCADOPAGO);
        if (!is_null($pm)) {
           $this->getPaymentService()->removePaymentMethod($pm); 
        }
        
        $this->getRequest()->getSession()->getFlashBag()->add(
            'notice',
            'Tu cuenta de Mercado Pago se ha desvinculado correctamente!'
        );
        
        return $this->getJSONResponse(['success' => TRUE, 'url' => $this->generateUrl('MozcuMozcuBundle_account')]);
    }
    
    public function mercadopagoCheckoutAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        $album = $this->getRepository('MozcuMozcuBundle:Album')->find($request->get('albumId'));
        if (!is_null($album)) {
            $checkoutId = $request->get('checkoutId');
            $price = $request->get('price');
            $checkoutUrl = $this->getPaymentService()->createMercadopagoCheckout($album, $checkoutId, $price);
            $this->getPaymentService()->updateCheckout($checkoutId, $price);
            return $this->getJSONResponse(['success' => true, 'checkout_url' => $checkoutUrl]);
        } else {
            return $this->getJSONResponse(['success' => false, 'message' => 'Invalid album id']);
        }        
    }
}
