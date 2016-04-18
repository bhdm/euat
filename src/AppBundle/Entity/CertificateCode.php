<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;

/**
 * CertificateCode
 *
 * @ORM\Table(name="certificate_code")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CertificateCodeRepository")
 */
class CertificateCode
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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var Course
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course", inversedBy="codes")
     */
    private $course;

    /**
     * @var RecordBook
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\RecordBook", inversedBy="code")
     */
    private $recordBook;

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
     * Set code
     *
     * @param string $code
     *
     * @return CertificateCode
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param Course $course
     */
    public function setCourse($course)
    {
        $this->course = $course;
    }

    /**
     * @return RecordBook
     */
    public function getRecordBook()
    {
        return $this->recordBook;
    }

    /**
     * @param RecordBook $recordBook
     */
    public function setRecordBook($recordBook)
    {
        $this->recordBook = $recordBook;
    }

    
}

