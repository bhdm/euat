<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event
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
     * @var string EAT|Partner
     *
     * @ORM\Column(name="owner", type="string", length=255, nullable=true)
     */
    private $owner;


    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime", nullable=true)
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timeStart", type="time", nullable=true)
     */
    private $timeStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timeEnd", type="time", nullable=true)
     */
    private $timeEnd;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City", inversedBy="events")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="adrs", type="string", length=255, nullable=true)
     */
    private $adrs;

    /**
     * @var bool
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="events")
     */
    private $category;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Specialty", inversedBy="events")
     * @ORM\OrderBy({"sort" = "ASC", "id" = "DESC"})
     */
    private $specialties;

    /**
     * @var boolean
     * @ORM\Column(name="allowCommentary", type="boolean", nullable=true)
     */
    private $allowCommentary;

    /**
     * @var Comment
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="event")
     */
    private $comments;

    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Partner", inversedBy="events")
     */
    private $partners;

    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Partner", inversedBy="sponsorEvents")
     * @ORM\JoinTable(name="sponsorEvent_partner")
     */
    private $sponsors;

    /**
     * @var string
     *
     * @ORM\Column(name="contacts", type="string", nullable=true)
     */
    private $contacts;

    /**
     * @var boolean
     *
     * @ORM\Column(name="theses", type="boolean", nullable=true)
     */
    private $theses;

    /**
     * @var boolean
     *
     * @ORM\Column(name="register", type="boolean", nullable=true)
     */
    private $register;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EventItem", mappedBy="event", cascade={"persist"})
     */
    private $items;

    /**
     * @var boolean
     * @ORM\Column(name="digest", type="boolean", nullable=true)
     */
    private $digest;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->specialties = new ArrayCollection();
        $this->partners = new ArrayCollection();
        $this->sponsors = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->enabled = true;
        $this->created = new \DateTime();
        $this->theses = false;
        $this->register = false;
        $this->digest = true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
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
     * Set title
     *
     * @param string $title
     *
     * @return Event
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
     * @return Event
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
     * @return Event
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
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Event
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Event
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set adrs
     *
     * @param string $adrs
     *
     * @return Event
     */
    public function setAdrs($adrs)
    {
        $this->adrs = $adrs;

        return $this;
    }

    /**
     * Get adrs
     *
     * @return string
     */
    public function getAdrs()
    {
        return $this->adrs;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Event
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Event
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
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
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }


    /**
     * @return boolean
     */
    public function isType()
    {
        return $this->type;
    }

    /**
     * @param boolean $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return ArrayCollection
     */
    public function getSpecialties()
    {
        return $this->specialties;
    }

    /**
     * @param ArrayCollection $specialties
     */
    public function setSpecialties($specialties)
    {
        $this->specialties = $specialties;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param string $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
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
     * @return string
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param string $contacts
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * @return \DateTime
     */
    public function getTimeStart()
    {
        return $this->timeStart;
    }

    /**
     * @param \DateTime $timeStart
     */
    public function setTimeStart($timeStart)
    {
        $this->timeStart = $timeStart;
    }

    /**
     * @return \DateTime
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }

    /**
     * @param \DateTime $timeEnd
     */
    public function setTimeEnd($timeEnd)
    {
        $this->timeEnd = $timeEnd;
    }



    /**
     * @return string
     */
    public function getPartners()
    {
        return $this->partners;
    }

    /**
     * @param string $partners
     */
    public function setPartners($partners)
    {
        $this->partners = $partners;
    }

    /**
     * @return mixed
     */
    public function isTheses()
    {
        return $this->theses;
    }

    /**
     * @param mixed $theses
     */
    public function setTheses($theses)
    {
        $this->theses = $theses;
    }

    /**
     * @return boolean
     */
    public function isRegister()
    {
        return $this->register;
    }

    /**
     * @param boolean $register
     */
    public function setRegister($register)
    {
        $this->register = $register;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
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

    public function removeItem($item){
        $this->items->removeElement($item);
    }

    public function addItem($item){
        $this->items->add($item);
    }

    /**
     * @return string
     */
    public function getSponsors()
    {
        return $this->sponsors;
    }

    /**
     * @param string $sponsors
     */
    public function setSponsors($sponsors)
    {
        $this->sponsors = $sponsors;
    }

    

}


