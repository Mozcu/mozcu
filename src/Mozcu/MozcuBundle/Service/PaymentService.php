<?php

namespace Mozcu\MozcuBundle\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Session\Session;

use Mozcu\MozcuBundle\Document\Purchase,
    Mozcu\MozcuBundle\Entity\Album,
    Mozcu\MozcuBundle\Entity\User;

use Mozcu\MozcuBundle\Exception\ServiceException;

class PaymentService
{
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
    
    public function __construct(DocumentManager $dm, Session $session)
    {
        $this->dm = $dm;
        $this->session = $session;
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
}
