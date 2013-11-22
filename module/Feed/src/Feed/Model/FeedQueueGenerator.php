<?php
/**
 * User: Josh
 * Date: 19/11/2013
 * Time: 5:39 μμ
 */

namespace Feed\Model;


use Account\Entity\Account;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use ZendGData\Youtube;
use Game\Entity\Game;

class FeedQueueGenerator implements ServiceManagerAwareInterface
{

    /**
     * @var ArrayCollection $feeds
     */
    private $feeds;

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

    private $gameRepository;

    private $feedRepository;

    private $serviceManager;

    public function FeedQueueGenerator()
    {
        $this->game = null;
        $this->user = null;
    }

    public function update()
    {
        $user = $this->getUser();
        $interactedFeeds = $user->getInteractedFeedIds(true);
        $userFeedQueue = $user->getFeedQueue(false,$this->game);
        foreach ($userFeedQueue as $feed) {
            if (in_array($feed->getId(), $interactedFeeds)) $user->removeFeedQueue($feed);
        }
        $this->refill();
    }

    private function refill()
    {
        $yt = $this->getYoutubeInstance();
        $time = 'this_month';
        $maxResults = 50;
        $startIndex = 1;
        $tags = $this->game->getName() . ' game';
        $query = $yt->newVideoQuery();
        $query->setOrderBy('viewCount')
            ->setStartIndex($startIndex)
            ->setTime($time)
            ->setMaxResults($maxResults)
            ->setVideoQuery($tags);
        $user = $this->getUser();
        $em = $this->getEntityManager();
        $feedRepository = $this->getFeedRepository();
        $feedQueue = $user->getFeedQueue(false, $this->game);
        $interactedFeeds = $user->getInteractedFeedIds(true, null, true); // the user interacted videos' ids
        $feedQueueVideoIds = $user->getFeedQueueVideoIds($feedQueue); // the queue's video ids
        $combinedVideoIds = array_merge($feedQueueVideoIds, $interactedFeeds);
        $count = count($feedQueue);
        $feeds = $feedRepository->getVideoIdAssocArray();
        $added = 0;
        try {
            while ($count < 35) {
                while ($added < 50) {
                    $query->setStartIndex($startIndex);
                    $videoFeed = $yt->getVideoFeed($query);
                    if (!empty($videoFeed)) {
                        foreach ($videoFeed as $entry) {
                            if (!in_array($entry->getVideoId(), $combinedVideoIds)) {
                                $videoId = $entry->getVideoId();
                                if (array_key_exists($videoId, $feeds)) {
                                    $feed = $feeds[$videoId];
                                } else {
                                    $feed = \Feed\Entity\Feed::createFromEntry($entry, $this->game);
                                    $em->persist($feed);
                                }
                                $combinedVideoIds[] = $videoId;
                                $user->addFeedQueue($feed);
                                $feeds[$entry->getVideoId()] = $feed;
                                $count++;
                                $added++;
                            }
                        }
                        $startIndex++;
                    }
                }
            }
            $em->persist($user);
            $em->flush();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getYoutubeInstance()
    {
        $adapter = new \Zend\Http\Client\Adapter\Curl();
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, false);
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
        $httpClient = new \ZendGData\HttpClient();
        $httpClient->setAdapter($adapter);
        return new Youtube($httpClient);
    }

    public function getUser()
    {
        if (null === $this->user) {
            $this->user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());
        }
        return $this->user;
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

    /**
     * Get the game repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getGameRepository()
    {
        if (!$this->gameRepository) {
            $this->setGameRepository($this->getEntityManager()->getRepository('Game\Entity\Game'));
        }
        return $this->gameRepository;
    }

    /**
     * Set the game repository.
     *
     * @param $gameRepository
     */
    public function setGameRepository($gameRepository)
    {
        $this->gameRepository = $gameRepository;
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

}