<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="members")
 */
class Member {

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
	private $position;

	/**
	 * @ORM\Column(type="string")
	 * @ORM\Column(name="position_en")
	 */
	private $positionEn;
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $avatar;	
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $email;	
	
	public function getUpdateValidationGroup($data){
		$validationGroup = array();
		if($data['member']['name'] != $this->name){
			$validationGroup[] = 'name';
		}
		if($data['member']['position'] != $this->position){
			$validationGroup[] = 'position';
		}
		if($data['member']['positionEn'] != $this->positionEn){
			$validationGroup[] = 'positionEn';
		}	
		if(!empty($data['member']['avatar'])){
			$validationGroup[] = 'avatar';
		}
		if($data['member']['email'] != $this->email){
			$validationGroup[] = 'email';
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
	
	public function getPosition(){
		return $this->position;
	}
	
	public function setPosition($position){
		$this->position = $position;
	}
	
	public function getPositionEn(){
		return $this->positionEn;
	}
	
	public function setPositionEn($positionEn){
		$this->positionEn = $positionEn;
	}
	
	public function getAvatar(){
		return $this->avatar;
	}
	
	public function setAvatar($avatar){
		$this->avatar = $avatar;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function setEmail($email){
		$this->email = $email;
	}
	
}
