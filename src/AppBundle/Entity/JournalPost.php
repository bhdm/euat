<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 02.11.15
 * Time: 13:44
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class JournalPost extends BaseEntity{

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Journal", inversedBy="posts")
     */
    protected $journal;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $titleEn;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $descriptionEn;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $keywords;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $keywordsEn;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $bodyEn;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $author;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $authorEn;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $source;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $sourceEn;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $pages;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $pagesEn;

    /**
     * @var boolean
     * @ORM\Column(name="digest", type="boolean", nullable=true)
     */
    private $digest;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $file;

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

    public function __construct(){
        parent::__construct();
        $this->created = new \DateTime();
        $this->digest = true;
        $this->file = array();
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }



    /**
     * @return mixed
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * @param mixed $journal
     */
    public function setJournal($journal)
    {
        $this->journal = $journal;
    }

    /**
     * @return mixed
     */
    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    /**
     * @param mixed $descriptionEn
     */
    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;
    }

    /**
     * @return mixed
     */
    public function getBodyEn()
    {
        return $this->bodyEn;
    }

    /**
     * @param mixed $bodyEn
     */
    public function setBodyEn($bodyEn)
    {
        $this->bodyEn = $bodyEn;
    }

    /**
     * @return mixed
     */
    public function getTitleEn()
    {
        return $this->titleEn;
    }

    /**
     * @param mixed $titleEn
     */
    public function setTitleEn($titleEn)
    {
        $this->titleEn = $titleEn;
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param mixed $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return mixed
     */
    public function getKeywordsEn()
    {
        return $this->keywordsEn;
    }

    /**
     * @param mixed $keywordsEn
     */
    public function setKeywordsEn($keywordsEn)
    {
        $this->keywordsEn = $keywordsEn;
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    /**
     * @return mixed
     */
    public function getAuthorEn()
    {
        return $this->authorEn;
    }

    /**
     * @param mixed $authorEn
     */
    public function setAuthorEn($authorEn)
    {
        $this->authorEn = $authorEn;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getSourceEn()
    {
        return $this->sourceEn;
    }

    /**
     * @param mixed $sourceEn
     */
    public function setSourceEn($sourceEn)
    {
        $this->sourceEn = $sourceEn;
    }

    /**
     * @return mixed
     */
    public function getPagesEn()
    {
        return $this->pagesEn;
    }

    /**
     * @param mixed $pagesEn
     */
    public function setPagesEn($pagesEn)
    {
        $this->pagesEn = $pagesEn;
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
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

    

}