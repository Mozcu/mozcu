<?php

namespace Mozcu\MozcuBundle\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

use Mozcu\MozcuBundle\Document\Purchase,
    Mozcu\MozcuBundle\Entity\Album,
    Mozcu\MozcuBundle\Entity\User,
    Mozcu\MozcuBundle\Entity\Profile,
    Mozcu\MozcuBundle\Entity\PaymentMethod;

use Mozcu\MozcuBundle\Service\PaymentMethod\MercadoPagoService;

use Mozcu\MozcuBundle\Exception\ServiceException;

class PaymentService
{
    
    /**
     *
     * @var EntityManager
     */
    private $em;
    
    /**
     *
     * @var DocumentManager
     */
    private $dm;
    
    /**
     *
     * @var Session
     */
    private $session;
    
    /**
     *
     * @var MercadoPagoService
     */
    private $mercadoPagoService;
    
    public function __construct(EntityManager $em, DocumentManager $dm, Session $session, MercadoPagoService $mercadoPagoService)
    {
        $this->em = $em;
        $this->dm = $dm;
        $this->session = $session;
        $this->mercadoPagoService = $mercadoPagoService;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param string $paymentService
     * @param decimal $price
     * @param array $serviceData
     * @param \Mozcu\MozcuBundle\Entity\User $user
     * @return \Mozcu\MozcuBundle\Document\Purchase
     * @throws ServiceException
     */
    public function createPurchase(Album $album, $paymentService, $price, array $serviceData = [], User $user = null)
    {
        try {
            $purchase = new Purchase();
            $purchase->setAlbum($album->getId())
                ->setAlbumName($album->getName())
                ->setPaymentService($paymentService)
                ->setPrice($price);
            
            if (!is_null($user)) {
               $purchase->setUser($user->getId())
                   ->setUsername($user->getUsername()); 
            }

            $this->dm->persist($purchase);
            $this->dm->flush();
            
            return $purchase;
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }
    
    public function createCheckout(Album $album)
    {
        $date = new \DateTime;
        $checkoutId = md5($album->getId() . $date->getTimestamp());
        
        $this->session->set($checkoutId, ['album_id' => $album->getId(), 'price' => 0]);
        
        return $checkoutId;
    }
    
    public function updateCheckout($id, $price)
    {
        if ($this->session->has($id)) {
            $checkout = $this->session->get($id);
            $checkout['price'] = $price;
            $this->session->set($id, $checkout);
            return $id;
        }
        throw new ServiceException("Checkout with id $id does not exist.");
    }


    public function getCheckout($id)
    {
        if ($this->session->has($id)) {
            return $this->session->get($id);
        }
        return null;
    }
    
    public function createPaymentMethod(Profile $profile, $type, array $data)
    {
        $pm = new PaymentMethod();
        $pm->setProfile($profile);
        
        if ($type == PaymentMethod::MERCADOPAGO) {
            $pm->setType($type);
            $response = $this->mercadoPagoService->authSeller($data['code'], $profile->getId());
            
            $date = new \DateTime();
            $response['expires_in'] = $date->getTimestamp() + $response['expires_in'];
            $pm->setData(json_encode($response));
        }
        
        $this->em->persist($pm);
        $this->em->flush();
        
        return $pm;
    }
    
    public function updatePaymentMethodData(PaymentMethod $paymentMethod, $data)
    {
        if ($paymentMethod->getType() == PaymentMethod::MERCADOPAGO) {
            $date = new \DateTime();
            $data['expires_in'] = $date->getTimestamp() + $data['expires_in'];
            $paymentMethod->setData(json_encode($data));
        }
        
        $this->em->flush();
        
        return $paymentMethod;
    }
    
    public function removePaymentMethod(PaymentMethod $pm)
    {
        $pm->setActive(FALSE);
        $this->em->flush();
    }
    
    public function createMercadopagoCheckout(Album $album, $checkoutId, $price)
    {
        if($album->getProfile()->hasPaymentMethod(PaymentMethod::MERCADOPAGO)) {
            $pm = $album->getProfile()->getPaymentMethod(PaymentMethod::MERCADOPAGO);
            $pmData = $pm->getDecodedData();
            
            $date = new \DateTime();
            $now = $date->getTimestamp();
            
            if ($pmData['expires_in'] <= $now ) {
                $pmData = $this->mercadoPagoService->refreshToken($pmData['refresh_token']); 
                $this->updatePaymentMethodData($pm, $pmData);
            }
            
            $response = $this->mercadoPagoService->generateAlbumCheckout($pmData['access_token'], $album, $checkoutId, $price);
            return $response['init_point'];
        }
    }
    
    
}
