<?php
namespace Evrika\MainBundle\Entity;

use
	Doctrine\ORM\Mapping as ORM,
	Symfony\Component\Validator\Constraints as Assert,
	Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass= "UniversityRepository")
 * @ORM\Table(name = "university")
 */
class University 
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type = "integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity = "Country", inversedBy = "universities")
	 * @Assert\NotBlank(message = "Пожалуйста, укажите страну ВУЗа.")
	 */
	protected $country;
	
	/**
	 * @var string
	 * @ORM\Column(type = "string")
	 * @Assert\NotBlank(message = "Пожалуйста, укажите название ВУЗа.")
	 */
	protected $title;
	
	public function __contsruct()
	{
		$this->doctors = new ArrayCollection();
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
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
		
		return $this;
	}
	
	public function getCountry()
	{
		return $this->country;
	}
	
	public function setCountry(Country $country)
	{
		$this->country = $country;
		
		return $this->country;
	}
	
	public function getDoctors()
	{
		return $this->doctors;
	}
	
	public function setDoctors(ArrayCollection $doctors)
	{
		$this->doctors = $doctors;
		
		return $this;
	}
}