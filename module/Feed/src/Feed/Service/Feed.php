<?php
/**
 * User: Josh
 * Date: 9/10/2013
 * Time: 1:04 μμ
 */

namespace Feed\Service;


use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Form\Form;
use Zend\Authentication\AuthenticationService;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Feed\Entity\Feed as FeedEntity;
use Doctrine\ORM\EntityManager;
use ZendGData\Youtube;

class Feed implements ServiceManagerAwareInterface
{

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \Feed\Model\FeedQueueGenerator
     */
    private $feedQueueGenerator;

    /**
     * @var Form
     */
    private $feedForm;

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $feedRepository;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $accountRepository;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $gameRepository;

    /**
     * Generates a feed list from youtube.
     *
     * @param String $game
     * @param int $startIndex
     * @param int $page
     * @return null|Paginator
     */
    public function generateFeedsFromYoutube($game)
    {
//        if (!empty($videoFeed)) {
//            $feedList = array();
//            $feedScores = array();
//            foreach ($videoFeed as $feed) {
//                $ytEntry = new \Feed\Model\YoutubeEntry($feed);
//                if ($ytEntry == null || $user->hasInteractedWithVideo($ytEntry->getVideoId())) continue;
//                $feedScores[] = $ytEntry->getScore();
//                $feedList[] = $ytEntry;
//            }
//            array_multisort($feedScores, SORT_DESC, $feedList);
//
//            $paginatorAdapter = new ArrayAdapter($feedList);
//            $paginator = new Paginator($paginatorAdapter);
//            return $paginator;
//        }
        $generator = $this->getFeedQueueGenerator();
        $generator->setGame($game);
        $generator->update();
        $user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());
        return $user->getFeedQueue(true,$game);
    }


    /**
     * Create and store a new feed.
     *
     * @param array $data
     * @return bool | Form
     */
    public function create($data)
    {
        $form = $this->getFeedForm();
        $entity = new FeedEntity();
        $form->bind($entity);
        $form->setData($data);
        if ($form->isValid()) {
            $entity = FeedEntity::create($entity, $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId()), $data['feed']['video']);
            $em = $this->getEntityManager();
            try {
                $em->persist($entity);
                $em->flush();
                return true;
            } catch (\Exception $e) {
                echo $e->getMessage();
                return false;
            }
        } else {
            return $form;
        }
    }

    public function rate($id,$type){
        /**
         * @var \Account\Entity\Account $user
         */
        $user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());
        /**
         * @var FeedEntity $feed
         */
        $feed = $this->getFeedRepository()->find($id);

        if($feed){
            $em = $this->getEntityManager();
            if($type == "like"){
                $user->addLikedFeeds($feed);
                $user->removeFeedQueue($feed);
            }else{
                $user->removeLikedFeeds($feed);
            }
            try {
                $em->persist($user);
                $em->flush();

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Add a feed to an account's watched ones.
     *
     * @param int $id
     * @return bool
     */
    public function addWatched($id)
    {
        /**
         * @var \Account\Entity\Account $user
         */
        $user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());
        /**
         * @var FeedEntity $feed
         */
        $feed = $this->getFeedRepository()->find($id);
        if ($feed) {
            $em = $this->getEntityManager();
            $user->addWatchedFeeds($feed);
            $user->removeFeedQueue($feed);
            $feed->setViews($feed->getViews()+1);
            try {
                $em->persist($user);
                $em->persist($feed);
                $em->flush();

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Add a feed to an account's watched ones.
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        /**
         * @var \Account\Entity\Account $user
         */
        $user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());
        /**
         * @var FeedEntity $feed
         */
        $feed = $this->getFeedRepository()->find($id);
        if ($feed) {
            $em = $this->getEntityManager();
            $user->addDeletedFeeds($feed);
            try {
                $em->persist($user);
                $em->flush();

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Add or remove a feed from the account's favorites.
     *
     * @param int $id
     * @param string $type
     * @return bool
     */
    public function setFavorite($id, $type, $activeGame = '')
    {
        /**
         * @var \Account\Entity\Account $user
         */
        $user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());
        $feed = $this->getFeedRepository()->find($id);
        $em = $this->getEntityManager();

        if ($feed) {

            if ($type == 'favorite') {
                $user->addFavoriteFeeds($feed);
                $user->removeFeedQueue($feed);
            } else {
                $user->removeFavoriteFeeds($feed);
                $user->addFeedQueue($feed);
            }
            try {
                $em->persist($user);
                $em->flush();

                return true;
            } catch (\Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return false;
    }

    /**
     * Get the feed repository.
     *
     * @return \Doctrine\ORM\EntityRepository
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
     * @return AuthenticationService
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
     * @return Feed
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }

    /**
     * Set the doctrine entity manager.
     *
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieve the doctrine entity manager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager)
            $this->setEntityManager($this->getServiceManager()->get('Doctrine\ORM\EntityManager'));
        return $this->entityManager;
    }

    /**
     * Gets the feed queue generator.
     *
     * @return \Feed\Model\FeedQueueGenerator
     */
    public function getFeedQueueGenerator(){
        if(null === $this->feedQueueGenerator)
            $this->setFeedQueueGenerator($this->getServiceManager()->get('feed_queue_generator'));
        return $this->feedQueueGenerator;
    }

    /**
     * Sets the feed queue generator.
     *
     * @param $feedQueueGenerator
     */
    public function setFeedQueueGenerator($feedQueueGenerator){
        $this->feedQueueGenerator = $feedQueueGenerator;
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
     * Set service manager.
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Retrieve the service manager.
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set the feed post form.
     *
     * @param Form $feedForm
     */
    public function setFeedForm(Form $feedForm)
    {
        $this->feedForm = $feedForm;
    }

    /**
     * Retrieve the feed post form.
     *
     * @return Form
     */
    public function getFeedForm()
    {
        if (null === $this->feedForm)
            $this->setFeedForm($this->getServiceManager()->get('feed_form'));
        return $this->feedForm;
    }


}