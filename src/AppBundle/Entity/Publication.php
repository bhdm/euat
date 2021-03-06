<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Publication
 *
 * @ORM\Table(name="publication")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PublicationRepository")
 */
class Publication
{
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;


    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", nullable=true)
     */
    private $source;


    /**
     * @var string
     *
     * @ORM\Column(name="anons", type="text", nullable=true)
     */
    private $anons;


    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var array
     *
     * @ORM\Column(name="preview", type="array")
     */
    private $preview;

    /**
     * @var array
     *
     * @ORM\Column(name="video", type="array")
     */
    private $video;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="publications")
     */
    private $category;

    /**
     * @var Specialty
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Specialty", inversedBy="publications")
     * @ORM\OrderBy({"sort" = "ASC", "id" = "ASC"})
     */
    private $specialties;

    /**
     * @var Comment
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="publication")
     */
    private $comments;

    /**
     * @var boolean
     * @ORM\Column(name="allowCommentary", type="boolean", nullable=true)
     */
    private $allowCommentary;

    /**
     * @var boolean
     * @ORM\Column(name="digest", type="boolean", nullable=true)
     */
    private $digest;

    /**
     * @ORM\Column(name="isPrivate", type="boolean", nullable=true)
     */
    private $private;

    /**
     * @var Gallery
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Gallery", mappedBy="publication")
     */
    private $images;

    /**
     * @var string
     *
     * @ORM\Column(name="metaTitle", type="string", length=255, nullable=true)
     */
    private $metaTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="metaDescription", type="string", length=255, nullable=true)
     */
    private $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKeyword", type="string", length=255, nullable=true)
     */
    private $metaKeyword;

    /**
     * @var string
     *
     * @ORM\Column(name="filter", type="string", length=255, nullable=true)
     */
    private $filter;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->digest = true;
        $this->allowCommentary = true;
        $this->enabled = true;
        $this->created = new \DateTime();
        $this->preview = array();
        $this->video = array();
        $this->specialties = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->private = false;
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

    public function setId($id){
        $this->id = $id;
    }
    /**
     * Set title
     *
     * @param string $title
     *
     * @return Publication
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Publication
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
     * Set body
     *
     * @param string $body
     *
     * @return Publication
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return array
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * @param array $preview
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
    }

    /**
     * @return Specialty
     */
    public function getSpecialties()
    {
        return $this->specialties;
    }

    /**
     * @param Specialty $specialties
     */
    public function setSpecialties($specialties)
    {
        $this->specialties = $specialties;
    }

    /**
     * @return Comment
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return boolean
     */
    public function isAllowCommentary()
    {
        return $this->allowCommentary;
    }

    /**
     * @param boolean $allowCommentary
     */
    public function setAllowCommentary($allowCommentary)
    {
        $this->allowCommentary = $allowCommentary;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getAnons()
    {
        return $this->anons;
    }

    /**
     * @param string $anons
     */
    public function setAnons($anons)
    {
        $this->anons = $anons;
    }

    /**
     * @return array
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param array $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * @return boolean
     */
    public function isDigest()
    {
        return $this->digest;
    }

    /**
     * @param boolean $digest
     */
    public function setDigest($digest)
    {
        $this->digest = $digest;
    }

    /**
     * @return mixed
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * @param mixed $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    }

    /**
     * @return Gallery
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param Gallery $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return ($this->metaTitle == null ? $this->title : $this->metaTitle);
    }

    /**
     * @param string $metaTitle
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string
     */
    public function getMetaKeyword()
    {
        return $this->metaKeyword;
    }

    /**
     * @param string $metaKeyword
     */
    public function setMetaKeyword($metaKeyword)
    {
        $this->metaKeyword = $metaKeyword;
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param string $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }


}

