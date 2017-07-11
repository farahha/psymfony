<?php

namespace Library\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application
 *
 * @ORM\Table(name="applications")
 * @ORM\Entity(repositoryClass="Library\PlatformBundle\Repository\ApplicationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Application
{
    /**
     * @var Advert
     *
     * @ORM\ManyToOne(targetEntity="Library\PlatformBundle\Entity\Advert", inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $advert;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=20)
     */
    private $applicationIpAddress;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Application
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Application
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Application
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set advert
     *
     * @param \Library\PlatformBundle\Entity\Advert $advert
     *
     * @return Application
     */
    public function setAdvert(\Library\PlatformBundle\Entity\Advert $advert)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \Library\PlatformBundle\Entity\Advert
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Permet de mettre à jour le nbApplications de l'Advert lorsqu'une nouvelle candidature (Application) est persistée en BDD
     * @ORM\PrePersist
     */
    public function increase()
    {
        $this->advert->increaseNbApplications();
    }

    /**
     * Permet de mettre à jour le nbApplications de l'Advert lorsqu'une nouvelle candidature (Application) est supprimée de BDD
     * @ORM\PreRemove
     */
    public function decrease()
    {
        $this->advert->decreaseNbApplications();
    }


    /**
     * Set applicationIpAddress
     *
     * @param string $applicationIpAddress
     *
     * @return Application
     */
    public function setApplicationIpAddress($applicationIpAddress)
    {
        $this->applicationIpAddress = $applicationIpAddress;

        return $this;
    }

    /**
     * Get applicationIpAddress
     *
     * @return string
     */
    public function getApplicationIpAddress()
    {
        return $this->applicationIpAddress;
    }
}
