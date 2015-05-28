<?php


namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Repository\PaymentMethodRepository")
 * @ORM\Table(name="payment_method")
 */
class PaymentMethod
{
    const MERCADOPAGO = 'mercadopago';    
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;
    
    /**
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="paymentMethods")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     **/
    private $profile;
    
    /**
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    public function __construct()
    {
        $this->active = true;
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return PaymentMethod
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return PaymentMethod
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * 
     * @return array
     */
    public function getDecodedData()
    {
        return json_decode($this->data, TRUE);
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return PaymentMethod
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PaymentMethod
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set profile
     *
     * @param \Mozcu\MozcuBundle\Entity\Profile $profile
     * @return PaymentMethod
     */
    public function setProfile(\Mozcu\MozcuBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \Mozcu\MozcuBundle\Entity\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
