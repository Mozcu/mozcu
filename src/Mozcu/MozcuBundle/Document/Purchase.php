<?php

namespace Mozcu\MozcuBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="Mozcu\MozcuBundle\Repository\PurchaseRepository")
 */
class Purchase
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Int
     */
    protected $album;
    
    /**
     * @MongoDB\Int
     */
    protected $user;

    /**
     * @MongoDB\String
     */
    protected $paymentService;
    
    /**
     * @MongoDB\String
     */
    protected $transactionId;
    
    /**
     * @MongoDB\String
     */
    protected $buyerId;
    
    /**
     * @MongoDB\Float
     */
    protected $price;
    
    /**
     * @MongoDB\Date
     */
    protected $date;
    
    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set album
     *
     * @param int $album
     * @return self
     */
    public function setAlbum($album)
    {
        $this->album = $album;
        return $this;
    }

    /**
     * Get album
     *
     * @return int $album
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Set user
     *
     * @param int $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return int $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set paymentService
     *
     * @param string $paymentService
     * @return self
     */
    public function setPaymentService($paymentService)
    {
        $this->paymentService = $paymentService;
        return $this;
    }

    /**
     * Get paymentService
     *
     * @return string $paymentService
     */
    public function getPaymentService()
    {
        return $this->paymentService;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set date
     *
     * @param date $date
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->date;
    }
}
