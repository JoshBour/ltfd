<?php
/**
 * User: Josh
 * Date: 10/9/2013
 * Time: 1:06 μμ
 */

namespace Feed\Repository;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class FeedRepository extends EntityRepository
{

    public function findInteractedFeed($userId,$feedId){

        $query = $this->getEntityManager()->getConnection()->prepare(
            "SELECT account_id,feed_id FROM accounts_feeds_liked WHERE account_id = :accId AND feed_id = :feedId
            UNION
            SELECT account_id,feed_id FROM accounts_feeds_deleted WHERE account_id = :accId AND feed_id = :feedId
            UNION
            SELECT account_id,feed_id FROM accounts_feeds_favorites WHERE account_id = :accId AND feed_id = :feedId
            UNION
            SELECT account_id,feed_id FROM accounts_feeds_history WHERE account_id = :accId AND feed_id = :feedId "
        );
        $query->bindValue('accId',$userId);
        $query->bindValue('feedId',$feedId);
        $query->execute();
//        $query->s
//        $qb = $this->createQueryBuilder('f');
//        $qb->select('f')
//            ->add('from','deletedFeedsAccounts, f.deletedFeedsAccounts, f.favoritedFeedsAccounts')
//            ->where($qb->expr()->eq('fr.feed','?1'))
//            ->setParameter('1',$feedId);
//        $query = $qb->getQuery();
        return $query->fetch();
    }

    public function findFeedsByType($gameId, $type)
    {
        $feedTypes = \Feed\Entity\Feed::$feedTypes;
        if(!in_array($type,$feedTypes)) return false;

        // figure out which table to query
        switch($type){
            case "favorites":
                $table = "f.favoritedFeedsAccounts";
                break;
            case "history":
                $table = "f.watchedFeedsAccounts";
                break;
            case "leet" :
                $table = "f.likedFeedsAccounts";
                break;
            case "deleted":
                $table = "f.deletedFeedsAccounts";
                break;
            default:
                return false;
        }
        $qb = $this->createQueryBuilder('f');
        $qb->select('f')
            ->add('from', "\Feed\Entity\Feed f JOIN f.likedFeedsAccounts df")
            ->where($qb->expr()->eq('f.game', '?1'))
            ->setParameters(array('1' => $gameId));

        $query = $qb->getQuery();
        return new Paginator(new ArrayAdapter($query->getResult()), true);
    }

    public function getVideoIdAssocArray()
    {
        $feeds = $this->findAll();
        $ids = array();
        foreach ($feeds as $feed) {
            $ids[$feed->getVideoId()] = $feed;
        }
        return $ids;
    }

    public function findBySort($gameId, $sort, $maxResults = 20, $firstResult = 0)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select()
            ->where($qb->expr()->eq('f.game', ':gameId'));
        if ($sort == 'popular') {
            $qb->add('orderBy', ('Log10(ABS(f.rating) + 1) * SIGN(f.rating) + (UNIX_TIMESTAMP(f.postTime) / 300000) DESC'));
        } else if ($sort == 'new') {
            $qb->andWhere($qb->expr()->lt('f.postTime', ':date'))
                ->setParameter('date', date('Y-m-d H:i:s'))
                ->orderBy('f.postTime', 'DESC');
        } else {
            throw new InvalidArgumentException("The sorting is of invalid type");
        }
        $qb->setParameter('gameId', $gameId)
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults);

        $query = $qb->getQuery();
        return new Paginator(new ArrayAdapter($query->getResult()), true);

    }

    public function findLatestFeeds($category = 'all', $firstResults = 0, $maxResults = 20)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select()
            ->where($qb->expr()->gte('f.postTime', ':date'))
            ->setFirstResult($firstResults)
            ->setMaxResults($maxResults);

        $query = $qb->getQuery();
        return new Paginator(new ArrayAdapter($query->getResult()), true);
    }


    public function findRatedFeeds($userId, $rating, $firstResult = 0, $maxResults = 20)
    {
        if (!empty($userId) && ($rating == '1' || $rating == '0')) {
            $qb = $this->createQueryBuilder('f');
            $qb->select('f')
                ->add('from', 'Feed\Entity\Feed f LEFT JOIN f.ratings fr')
                ->where($qb->expr()->eq('fr.user', '?1'))
                ->andWhere($qb->expr()->eq('fr.rating', '?2'))
                ->orderBy('f.postTime', 'DESC')
                ->setParameters(array('1' => $userId, '2' => $rating))
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResults);

            $query = $qb->getQuery();
            return new Paginator(new ArrayAdapter($query->getResult()), true);
        } else {
            throw new InvalidArgumentException('The provided arguments are invalid.');
        }
    }
}