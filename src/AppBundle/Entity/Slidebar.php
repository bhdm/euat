<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Slidebar
 *
 * @ORM\Table(name="slidebar")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SlidebarRepository")
 */
class Slidebar
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;


    /**
     * @var array
     * @ORM\Column(name="file", type="array")
     */
    private $file;

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


}

