<?php
namespace Account\Repository;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityRepository;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

class AccountRepository extends EntityRepository
{

    public function findDeletedFeedsByGame($gameId, $userId)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('f')
            ->add('from', '\Feed\Entity\Feed f JOIN a.deletedFeeds df')
            ->andWhere($qb->expr()->eq('f.game', '?2'))
            ->setParameters(array('2' => $gameId));

        $query = $qb->getQuery();
        return new Paginator(new ArrayAdapter($query->getResult()), true);
    }
}