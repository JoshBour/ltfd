<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Admin\Repository\GeneralRepository")
 * @ORM\Table(name="general")
 */
class General{

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
	 * @ORM\Column(type="text")
	 */
	private $content;
	
	/**
	 * @ORM\Column(type="text")
	 * @ORM\Column(name="content_en")
	 */
	private $contentEn;
	
	public function getUpdateValidationGroup($data){
		$validationGroup = array();
		if($data->general['content'] != $this->content){
			$validationGroup[] = 'content';
		}
		if($data->general['contentEn'] != $this->contentEn){
			$validationGroup[] = 'contentEn';
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
	
	public function getContent(){
		return $this->content;
	}
	
	public function setContent($content){
		$this->content = $content;
	}
	
	public function getContentEn(){
		return $this->contentEn;
	}
	
	public function setContentEn($contentEn){
		$this->contentEn = $contentEn;
	}
}
