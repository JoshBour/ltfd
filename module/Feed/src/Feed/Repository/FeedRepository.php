<?php
/**
 * User: Josh
 * Date: 10/9/2013
 * Time: 1:06 μμ
 */

namespace Feed\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class FeedRepository extends EntityRepository{
    public function findFeedsByCategory($category, $userId,$firstResult = 0, $maxResults = 10)
    {
        if ($category) {
            $qb = $this->createQueryBuilder('f');
            $qb->select('f')
                ->add('from','\Feed\Entity\Feed f LEFT JOIN f.categorizedFeeds af')
             #   ->innerJoin('\Account\Entity\AccountsFeeds', 'af', 'WITH', 'af.feed = f.id')
                ->where($qb->expr()->eq('af.account','?1'))
                ->andWhere($qb->expr()->eq('af.category','?2'))
                ->setParameters(array('1' => $userId, '2' => $category))
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults);

            return new Paginator($qb->getQuery(),true);
        } else {
            throw new InvalidArgumentException('The provided arguments are invalid.');
        }
    }


    public function findRatedFeeds($userId, $rating,$firstResult = 0, $maxResults = 10)
    {
        if(!empty($userId) && ($rating == '1' || $rating == '0')){
            $qb = $this->createQueryBuilder('f');
            $qb->select('f')
                ->add('from', 'Feed\Entity\Feed f LEFT JOIN f.ratings fr')
                ->where($qb->expr()->eq('fr.user','?1'))
                ->andWhere($qb->expr()->eq('fr.rating','?2'))
                ->setParameters(array('1' => $userId,'2' =>$rating))
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults);

            return new Paginator($qb->getQuery(),true);
        }else {
            throw new InvalidArgumentException('The provided arguments are invalid.');
        }
    }
}