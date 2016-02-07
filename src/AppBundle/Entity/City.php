<?php
namespace AppBundle\Entity;

use
	Doctrine\ORM\Mapping as ORM,
	Symfony\Component\Validator\Constraints as Assert,
	Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name = "geoCity")
 */
class City
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type = "integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity = "Country", inversedBy = "cities")
	 * @Assert\NotBlank(message = "Пожалуйста, укажите страну.")
	 */
	protected $country;

	/**
	 * @var string
	 * @ORM\Column(type = "string")
	 * @Assert\NotBlank(message = "Пожалуйста, укажите название города.")
	 * @Assert\Length(max = 63, maxMessage = "Название города не может быть длиннее {{limit}}.")
	 */
	protected $title;

	public function __construct()
	{
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->title;
	}

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param mixed $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}


}