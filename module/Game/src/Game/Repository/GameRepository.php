<?php
namespace Game\Repository;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository{
    public function searchByName($name){
        if(!empty($name)){
            $qb = $this->createQueryBuilder('g');
            $qb->where($qb->expr()->like('g.name','?1'));
            $qb->setParameter(1, $name.'%');
            return $qb->getQuery()->getResult();
        }else{
            throw new InvalidArgumentException("The game's name can't be empty.");
        }
    }
}