<?php
/**
 * User: Josh
 * Date: 10/9/2013
 * Time: 1:06 μμ
 */

namespace Feed\Repository;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class FeedRepository extends EntityRepository{
//    public function findFeedsByCategory($category, $userId,$firstResult = 0, $maxResults = 10)
//    {
//        if ($category) {
//            $qb = $this->createQueryBuilder('f');
//            $qb->select('f')
//                ->add('from','\Feed\Entity\Feed f LEFT JOIN f.categorizedFeeds af')
//             #   ->innerJoin('\Account\Entity\AccountsFeeds', 'af', 'WITH', 'af.feed = f.id')
//                ->where($qb->expr()->eq('af.account','?1'))
//                ->andWhere($qb->expr()->eq('af.category','?2'))
//                ->orderBy('f.postTime','DESC')
//                ->setParameters(array('1' => $userId, '2' => $category))
//                ->setFirstResult($firstResult)
//                ->setMaxResults($maxResults);
//
//            return new Paginator($qb->getQuery(),true);
//        } else {
//            throw new InvalidArgumentException('The provided arguments are invalid.');
//        }
//    }

    public function findBySort($gameId,$sort,$category = null,$firstResult = 0,$maxResults = 20){
        $qb = $this->createQueryBuilder('f');
        $qb->select()
            ->where($qb->expr()->eq('f.game',':gameId'));
        if($sort == 'popular'){
            $qb->add('orderBy', ('Log10(ABS(f.rating) + 1) * SIGN(f.rating) + (UNIX_TIMESTAMP(f.postTime) / 300000) DESC'));
        }else if($sort == 'new'){
            $qb->andWhere($qb->expr()->lt('f.postTime',':date'))
                ->setParameter('date',date('Y-m-d H:i:s'))
                ->orderBy('f.postTime','DESC');
        }else{
            throw new InvalidArgumentException("The sorting is of invalid type");
        }
        if($category){
            $qb->andWhere($qb->expr()->eq('f.category',':category'))
                ->setParameter('category',$category);
        }
        $qb->setParameter('gameId',$gameId)
            ->setFirstResult($firstResult)
            ->setMaxResults(20);

        return new Paginator($qb->getQuery(),true);

    }

    public function findLatestFeeds($category = 'all',$firstResults = 0,$maxResults = 20){
        $qb = $this->createQueryBuilder('f');
        $qb->select()
            ->where($qb->expr()->gte('f.postTime', ':date'))
            ->setFirstResult($firstResults)
            ->setMaxResults($maxResults);

        echo $qb->getParameter('date')->getValue();
        return new Paginator($qb->getQuery(),true);
    }


    public function findRatedFeeds($userId, $rating,$firstResult = 0, $maxResults = 20)
    {
        if(!empty($userId) && ($rating == '1' || $rating == '0')){
            $qb = $this->createQueryBuilder('f');
            $qb->select('f')
                ->add('from', 'Feed\Entity\Feed f LEFT JOIN f.ratings fr')
                ->where($qb->expr()->eq('fr.user','?1'))
                ->andWhere($qb->expr()->eq('fr.rating','?2'))
                ->orderBy('f.postTime','DESC')
                ->setParameters(array('1' => $userId,'2' =>$rating))
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults);

            return new Paginator($qb->getQuery(),true);
        }else {
            throw new InvalidArgumentException('The provided arguments are invalid.');
        }
    }
}