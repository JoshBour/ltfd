<?php
/**
 * User: Josh
 * Date: 19/11/2013
 * Time: 5:39 μμ
 */

namespace Feed\Model;


use Account\Entity\Account;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use ZendGData\Youtube;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Game\Entity\Game;

class FeedQueueGenerator implements ServiceManagerAwareInterface
{
    private $startIndex;

    /**
     * @var Game $game
     */
    private $game;

    /**
     * @var Account
     */
    private $user;


    private $authService;

    private $entityManager;

    private $accountRepository;

    private $feedRepository;

    private $serviceManager;

    public function FeedQueueGenerator()
    {
        $this->game = null;
        $this->user = null;
        $this->interactedFeeds = null;
        $this->feedQueue = null;
        $this->startIndex = 1;
    }

    public function update($page = 1)
    {
        $user = $this->getUser();
        $em = $this->getEntityManager();
        $userFeedQueue = $user->getFeedQueue(false, $this->game);
        $feedRepository = $this->getFeedRepository();
        $persist = false;
        foreach ($userFeedQueue as $feed) {
            $hasInteracted = $feedRepository->findInteractedFeed($user->getId(), $feed->getId());
            if (!empty($hasInteracted)) {
                $persist = true;
                $user->removeFeedQueue($feed);
            }
        }
        if ($persist) {
            $em->persist($user);
            $em->flush();
        }
        $feedCount = count($userFeedQueue);

        if($this->startIndex != 1){
            $requiredFeeds = $page*35;
            if($feedCount < $requiredFeeds){
                $this->fetch($userFeedQueue,$feedCount - $requiredFeeds);
            }
        }else{
            if (count($userFeedQueue) < 35) $this->refill($userFeedQueue);
        }
        return new Paginator(new ArrayAdapter($userFeedQueue));
    }

    public function refill(&$queue = null)
    {
        if ($queue === null) $queue = $this->getUser()->getFeedQueue(false, $this->game);
        $this->fetch($queue, 35 - count($queue));
    }

    private function fetch(&$queue, $feedsToAdd = 50)
    {
        $yt = $this->getYoutubeInstance();
        $em = $this->getEntityManager();
        $query = $this->generateQueryString();
        $user = $this->getUser();
        $feedRepository = $this->getFeedRepository();
        $added = 0;
        while ($added < $feedsToAdd) {
            try {
                $query->setStartIndex($this->startIndex);
                $videoFeed = $yt->getVideoFeed($query);
                foreach ($videoFeed as $entry) {
                    $videoId = $entry->getVideoId();
                    $feed = $feedRepository->findOneBy(array('videoId' => $videoId));
                    if ($feed) {
                        $isInQueue = in_array($feed, $queue);
                        if ($isInQueue) continue;
                    } else {
                        $feed = \Feed\Entity\Feed::createFromEntry($entry, $this->game);
                    }
                    $em->persist($feed);
                    $user->addFeedQueue($feed);
                    $added++;
                }
                $this->startIndex++;
            } catch (\Exception $e) {
                return false;
            }
        }
        $em->persist($user);
        $em->flush();
        return true;
    }

    /**
     * @param int $startIndex
     * @param int $maxResults
     * @param string $time
     * @return \ZendGData\YouTube\VideoQuery
     */
    private function generateQueryString($startIndex = 1, $maxResults = 50, $time = "this_month")
    {
        $yt = $this->getYoutubeInstance();
        $query = $yt->newVideoQuery();
        $query->setOrderBy('viewCount')
            ->setStartIndex($startIndex)
            ->setTime($time)
            ->setMaxResults($maxResults)
            ->setVideoQuery($this->game->getName() . ' game');
        return $query;
    }

    /**
     * @return Youtube
     */
    private function getYoutubeInstance()
    {
        $adapter = new \Zend\Http\Client\Adapter\Curl();
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, false);
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
        $httpClient = new \ZendGData\HttpClient();
        $httpClient->setAdapter($adapter);
        return new Youtube($httpClient);
    }

    /**
     * Retrieve the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        }
        return $this->entityManager;
    }

    /**
     * Set the doctrine entity manager
     *
     * @param EntityManager $entityManager
     * @return Account
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return Account
     */
    public function getUser()
    {
        if (null === $this->user) {
            $this->user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());
        }
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getGame()
    {
        return $this->game;
    }

    public function setGame($game)
    {
        $this->game = $game;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return Account
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Get the account repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAccountRepository()
    {
        if (!$this->accountRepository) {
            $this->setAccountRepository($this->getEntityManager()->getRepository('Account\Entity\Account'));
        }
        return $this->accountRepository;
    }

    public function getStartIndex()
    {
        return $this->startIndex;
    }

    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
    }

    /**
     * Set the account repository.
     *
     * @param $accountRepository
     */
    public function setAccountRepository($accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * getAuthService
     *
     * @return\Zend\Authentication\AuthenticationService AuthenticationService
     */
    public function getAuthService()
    {
        if (null === $this->authService) {
            $this->authService = $this->getServiceManager()->get('auth_service');
        }
        return $this->authService;
    }

    /**
     * setAuthenticationService
     *
     * @param AuthenticationService $authService
     * @return Account
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }

    /**
     * Get the feed repository.
     *
     * @return \Feed\Repository\FeedRepository
     */
    public function getFeedRepository()
    {
        if (!$this->feedRepository) {
            $this->setFeedRepository($this->getEntityManager()->getRepository('Feed\Entity\Feed'));
        }
        return $this->feedRepository;
    }

    /**
     * Set the feed repository.
     *
     * @param $feedRepository
     */
    public function setFeedRepository($feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }
}