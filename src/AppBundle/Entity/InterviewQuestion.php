<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * InterviewQuestion
 *
 * @ORM\Table(name="interview_question")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterviewQuestionRepository")
 */
class InterviewQuestion
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\InterviewChoice", mappedBy="question")
     */
    private $choices;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Interview", inversedBy="questions")
     */
    private $interview;


    public function __construct()
    {
        $this->choices = new ArrayCollection();
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
     * @return InterviewQuestion
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
     * @return mixed
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param mixed $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }

    /**
     * @return mixed
     */
    public function getInterview()
    {
        return $this->interview;
    }

    /**
     * @param mixed $interview
     */
    public function setInterview($interview)
    {
        $this->interview = $interview;
    }


}

