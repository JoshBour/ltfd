<?php

namespace Admin\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository{
	
	public function findByNot($field,$values){
		/**
		 * @var Doctrine\ORM\QueryBuilder
		 */
		$qb = $this->createQueryBuilder(('c'));
		$qb->where($qb->expr()->not($qb->expr()->eq('a')));
	    $qb->where($qb->expr()->not($qb->expr()->eq('a.'.$field, '?1')));
	    $qb->setParameter(1, $value);
	
	    return $qb->getQuery()
	        ->getResult();	
	}
	
	public function findByNameOrTranslatedName($value){
		/**
		 * @var Doctrine\ORM\QueryBuilder
		 */		
		$qb = $this->createQueryBuilder('c');
		$qb->where($qb->expr()->eq('c.name', '?1'))
			->orWhere($qb->expr()->eq('c.nameEn', '?1'))
			->setParameter(1, $value);
			
		return $qb->getQuery()->getResult();
	}
}
