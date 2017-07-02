<?php

namespace Tests\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Tests\PlatformBundle\Validator\AntiFlood;

/**
 * Advert
 *
 * @ORM\Table(name="adverts")
 * @ORM\Entity(repositoryClass="Tests\PlatformBundle\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="title", message="Une annonce existe déjà avec ce titre")
 */
class Advert
{
    /**
    * @ORM\OneToOne(targetEntity="Tests\PlatformBundle\Entity\Image", cascade={"persist", "remove"})
    * @Assert\Valid()
    */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="Tests\PlatformBundle\Entity\Category", cascade={"persist"})
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="Tests\PlatformBundle\Entity\Application", mappedBy="advert")
     */
    private $applications;

    /**
     * @ORM\OneToMany(targetEntity="Tests\PlatformBundle\Entity\AdvertSkill", mappedBy="advert", cascade={"remove"})
     */
    private $skills;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     * @Assert\Length(min=10, minMessage="Minimum 10 caractères")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\Length(min=2, minMessage="Minimum 2 caractères")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     * @Assert\NotBlank()
     * @AntiFlood(message="Merci de decrire d'avantage votre annonce.")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_applications", type="integer")
     */
    private $nbApplications = 0;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;

    /**
     *
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->categories = new ArrayCollection();
        $this->applications = new ArrayCollection();
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Advert
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
     * Set title
     *
     * @param string $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Advert
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
     * @return Advert
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
     * Set published
     *
     * @param boolean $published
     *
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set image
     *
     * @param \Tests\PlatformBundle\Entity\Image $image
     *
     * @return Advert
     */
    public function setImage(\Tests\PlatformBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Tests\PlatformBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add category
     *
     * @param \Tests\PlatformBundle\Entity\Category $category
     *
     * @return Advert
     */
    public function addCategory(\Tests\PlatformBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Tests\PlatformBundle\Entity\Category $category
     */
    public function removeCategory(\Tests\PlatformBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add application
     *
     * @param \Tests\PlatformBundle\Entity\Application $application
     *
     * @return Advert
     */
    public function addApplication(\Tests\PlatformBundle\Entity\Application $application)
    {
        $this->applications[] = $application;

        $application->setAdvert($advert); // Lier l'annonce a la candidature

        return $this;
    }

    /**
     * Remove application
     *
     * @param \Tests\PlatformBundle\Entity\Application $application
     */
    public function removeApplication(\Tests\PlatformBundle\Entity\Application $application)
    {
        $this->applications->removeElement($application);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Advert
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Permet de mettre à jour l'attribut updated avec la date de la dernière mise à jour de l'advert
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->setUpdated(new \DateTime());
    }

    public function increaseNbApplications()
    {
        $this->nbApplications++;
    }

    public function decreaseNbApplications()
    {
        $this->nbApplications--;
    }

    /**
     * Set nbApplications
     *
     * @param integer $nbApplications
     *
     * @return Advert
     */
    public function setNbApplications($nbApplications)
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    /**
     * Get nbApplications
     *
     * @return integer
     */
    public function getNbApplications()
    {
        return $this->nbApplications;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Advert
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add skill
     *
     * @param \Tests\PlatformBundle\Entity\AdvertSkill $skill
     *
     * @return Advert
     */
    public function addSkill(\Tests\PlatformBundle\Entity\AdvertSkill $skill)
    {
        $this->skills[] = $skill;

        return $this;
    }

    /**
     * Remove skill
     *
     * @param \Tests\PlatformBundle\Entity\AdvertSkill $skill
     */
    public function removeSkill(\Tests\PlatformBundle\Entity\AdvertSkill $skill)
    {
        $this->skills->removeElement($skill);
    }

    /**
     * Get skills
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @Assert\Callback
     */
    public function isContentValid(ExecutionContextInterface $context)
    {
        $forbiddenWords = ['démotivation', 'abandon'];
        if (preg_match('#'.implode('|', $forbiddenWords).'#', $this->getContent())) {
            $context->buildViolation('Contenu invalide car il contient un mot interdit')
            ->atPath('content')
            ->addViolation();
        }
    }
}
