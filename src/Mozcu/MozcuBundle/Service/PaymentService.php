<?php

namespace Mozcu\MozcuBundle\Service;

use Doctrine\ODM\MongoDB\DocumentManager;

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
    
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
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
                ->setPaymentService($paymentService)
                ->setPrice($price);
            if (!is_null($user)) {
               $purchase->setUser($user->getId()); 
            }

            $this->dm->persist($purchase);
            $this->dm->flush();
            
            return $purchase;
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }
}
