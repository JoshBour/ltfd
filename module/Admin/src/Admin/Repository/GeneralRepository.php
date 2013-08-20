<?php

namespace Admin\Repository;

use Doctrine\ORM\EntityRepository;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class GeneralRepository extends EntityRepository{
	
	public function getGeneralArray($locale = 'nb_NO'){
		$hydrator = new DoctrineObject($this->getEntityManager(), '\Admin\Entity\General');
		$general = $this->findAll();
		$type = 'content';
		$result = array();
		
		if($locale != 'nb_NO'){
			$type = 'contentEn';
		}
		
		foreach($general as $content){
			$dataArray = $hydrator->extract($content);
			$result[$dataArray['name']] = $dataArray[$type];
		}
		
		return $result;
	}
}
