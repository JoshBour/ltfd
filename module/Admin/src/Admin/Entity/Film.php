<?php

namespace Admin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Admin\Repository\FilmRepository")
 * @ORM\Table(name="films")
 */
class Film {

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 * @ORM\Column(length=11)
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
	 * @ORM\Column(type="string")
	 */
	private $description;

	/**
	 * @ORM\Column(type="string")
	 * @ORM\Column(name="description_en")
	 */
	private $descriptionEn;

	/**
	 * @ORM\Column(type="string")
	 * @ORM\Column(name="video")
	 */
	private $video;

	/**
	 * @ORM\Column(type="date")
	 * @ORM\Column(name="post_time")
	 */
	private $postTime;

	/**
	 * @ORM\Column(type="string")
	 */
	private $snapshot;

	/**
	 * @ORM\Column(type="smallint")
	 * @ORM\Column(name="in_slide")
	 */
	private $inSlide;

	/**
	 * @ORM\ManyToMany(targetEntity="Admin\Entity\Category")
	 * @ORM\JoinTable(name="films_categories",
	 * 				joinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName = "id")},
	 * 				inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")})
	 */
	private $categories;

	public function __construct() {
		$this -> categories = new ArrayCollection();
	}

	public function getUpdateValidationGroup($data) {
		$validationGroup = array();
		if ($data['film']['name'] != $this -> name) {
			$validationGroup[] = 'name';
		}
		if ($data['film']['nameEn'] != $this -> nameEn) {
			$validationGroup[] = 'nameEn';
		}
		if ($data['film']['description'] != $this -> description) {
			$validationGroup[] = 'description';
		}
		if ($data['film']['descriptionEn'] != $this -> descriptionEn) {
			$validationGroup[] = 'descriptionEn';
		}
		if ($data['film']['postTime'] != $this -> postTime) {
			$validationGroup[] = 'postTime';
		}
		if($data['film']['inSlide'] != $this->inSlide){
			$validationGroup[] = 'inSlide';
		}
		if ($data['film']['video'] != $this->video) {
			$validationGroup[] = 'video';
		}
		if (!empty($data['film']['snapshot'])) {
			$validationGroup[] = 'snapshot';
		}
		if (!empty($data['film']['categories'])) {
			$ids = array();
			foreach ($this->categories as $category) {
				$ids[] = $category -> getId();
			}
			if ($ids != $data['film']['categories']) {
				$validationGroup[] = 'categories';
			}
		}
		return $validationGroup;
	}

	public function getImageThumb($x, $y){
		$extension = substr($this->snapshot, -4);
		$mainPart = substr($this->snapshot, 0, -4);
		return $mainPart . '-' . $x . '-' . $y . $extension;
	}

	public function getCategoriesNames() {
		$names = array();
		foreach ($this->categories as $category) {
			$names[] = $category -> getName();
		}
		return $names;
	}

	public function getVideoId(){
		$id = preg_match("#^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)#",$this->video, $matches);
		return $matches[5];
	}
	
	function getVimeoThumb($size = "medium") {
	    $data = file_get_contents("http://vimeo.com/api/v2/video/" . $this->getVideoId() . ".json");
	    $data = json_decode($data);
		if($size == 'small'){
			return $data[0]->thumbnail_small;
		}else if($size == 'large'){
			return $data[0]->thumbnail_large;
		}
	    return $data[0]->thumbnail_medium;
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

	public function getDescription() {
		return $this -> description;
	}

	public function setDescription($description) {
		$this -> description = $description;
	}

	public function getDescriptionEn() {
		return $this -> descriptionEn;
	}

	public function setDescriptionEn($descriptionEn) {
		$this -> descriptionEn = $descriptionEn;
	}

	public function getVideo() {
		return $this -> video;
	}

	public function setVideo($video) {
		$this -> video = $video;
	}

	public function getPostTime() {
		return $this -> postTime;
	}

	public function setPostTime($postTime) {
		$this -> postTime = $postTime;
	}

	public function getSnapshot() {
		return $this -> snapshot;
	}

	public function setSnapshot($snapshot) {
		$this -> snapshot = $snapshot;
	}
	
	public function getInSlide(){
		return $this->inSlide;
	}
	
	public function setInSlide($inSlide){
		$this->inSlide = $inSlide;
	}

	/**
	 * @return Collection
	 */
	public function getCategories() {
		return $this -> categories;
	}

	public function setCategories(\Admin\Entity\Category $category) {
		$this -> categories[] = $category;
	}

	public function addCategories($categories) {
		foreach ($categories as $category)
			$this -> categories -> add($category);
	}

	/**
	 * @return Collection
	 */
	public function removeCategories($categories) {
		$this -> categories -> removeElement($categories);
	}

	public function clearCategories() {
		$this -> categories -> clear();
	}

}
