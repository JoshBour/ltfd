<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="\Admin\Repository\CategoryRepository")
 * @ORM\Table(name="categories")
 */
class Category {

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @ORM\Column(type="string")
	 * @ORM\Column(name="name_en")
	 */
	private $nameEn;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $position;

	/**
	 * @ORM\ManyToMany(targetEntity="Film", mappedBy="categories")
	 **/
	private $films;

	public function __construct() {
		$this -> films = new ArrayCollection();
	}

	public function getUpdateValidationGroup($data) {
		$validationGroup = array();
		if ($data -> category['name'] != $this -> name) {
			$validationGroup[] = 'name';
		}
		if ($data -> category['nameEn'] != $this -> nameEn) {
			$validationGroup[] = 'nameEn';
		}
		if ($data -> category['position'] != $this-> position){
			$validationGroup[] = 'position';
		}
		return $validationGroup;
	}

	public function getId() {
		return $this -> id;
	}

	public function setId($id) {
		$this -> id = $id;
	}

	public function getName() {
		return $this -> name;
	}

	public function setName($name) {
		$this -> name = $name;
	}

	public function getNameEn() {
		return $this -> nameEn;
	}

	public function setNameEn($nameEn) {
		$this -> nameEn = $nameEn;
	}
	
	public function getPosition(){
		return $this->position;
	}
	
	public function setPosition($position){
		$this->position = $position;
	}

	public function addFilms($films) {
		$this -> films -> add($films);
	}

	public function getFilms() {
		return $this -> films;
	}

}
