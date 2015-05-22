<?php

namespace Mozcu\MozcuBundle\Entity;

use Mozcu\MozcuBundle\Entity\AlbumImage;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mozcu\MozcuBundle\Repository\ImagePresentationRepository")
 * @ORM\Table(name="image_presentation")
 */
class ImagePresentation {
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $width;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $height;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $thumbnail;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;
    
    /**
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="presentations")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     **/
    private $image;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $static_file_name;
    
    public function __construct() {
        $this->thumbnail = false;
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
     * Set name
     *
     * @param string $name
     * @return ImagePresentation
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return ImagePresentation
     */
    public function setWidth($width)
    {
        $this->width = $width;
    
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return ImagePresentation
     */
    public function setHeight($height)
    {
        $this->height = $height;
    
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set thumbnail
     *
     * @param boolean $thumbnail
     * @return ImagePresentation
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    
        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return boolean 
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return ImagePresentation
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        if(empty($this->url) && $this->getImage() instanceof AlbumImage && $this->getImage()->getAlbum()->getIsActive()) {
            $albumId = $this->getImage()->getAlbum()->getId();
            $imageName = $this->getImage()->getTemporalFileName();
            $this->url = " https://storage.googleapis.com/static-mozcu/$albumId/$imageName";
        }
        return $this->url;
    }

    /**
     * Set image
     *
     * @param \Mozcu\MozcuBundle\Entity\Image $image
     * @return ImagePresentation
     */
    public function setImage(\Mozcu\MozcuBundle\Entity\Image $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Mozcu\MozcuBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set static_file_name
     *
     * @param string $staticFileName
     * @return ImagePresentation
     */
    public function setStaticFileName($staticFileName)
    {
        $this->static_file_name = $staticFileName;
    
        return $this;
    }

    /**
     * Get static_file_name
     *
     * @return string 
     */
    public function getStaticFileName()
    {
        return $this->static_file_name;
    }
}