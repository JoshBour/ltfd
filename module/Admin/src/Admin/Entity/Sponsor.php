<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sponsors")
 */
class Sponsor {

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
	 */
	private $url;	

	/**
	 * @ORM\Column(type="string")
	 */
	private $image;
	
	
	public function getUpdateValidationGroup($data){
		$validationGroup = array();
		if($data['sponsor']['name'] != $this->name){
			$validationGroup[] = 'name';
		}
		if($data['sponsor']['url'] != $this->url){
			$validationGroup[] = 'url';
		}
		if(!empty($data['sponsor']['image'])){
			$validationGroup[] = 'image';
		}
		return $validationGroup;	
	}	

	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function setUrl($url){
		$this->url = $url;
	}	
	
	public function getImage(){
		return $this->image;
	}
	
	public function setImage($image){
		$this->image = $image;
	}
	
}
