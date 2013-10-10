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
use Doctrine\ORM\EntityManager;

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
     * Create and store a new feed.
     *
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        $form = $this->getFeedForm();
        $entity = new \Feed\Entity\Feed();
        $form->bind($entity);
        $form->setData($data);
        if ($form->isValid()) {
            $entity = \Feed\Entity\Feed::create($entity, $this->getAuthService()->getIdentity(), $data['feed']['video']);
            $em = $this->getEntityManager();
            try {
                $em->persist($entity);
                $em->flush();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Add a feed to an account's watched ones.
     *
     * @param int $id
     * @return bool
     */
    public function addWatched($id){
        $feed = $this->getFeedRepository()->find($id);
        if($feed){
            $em = $this->getEntityManager();

            /**
             * @var \Account\Entity\Account $user
             */
            $user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());

            $user->addWatchedFeeds($feed);
            try{
                $em->persist($user);
                $em->flush;

                return true;
            }catch(\Exception $e){
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
    public function setFavorite($id, $type){
        $feed = $this->getFeedRepository()->find($id);
        if($feed){
            $em = $this->getEntityManager();

            /**
             * @var \Account\Entity\Account $user
             */
            $user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());
            if($type == 'favorite'){
                $user->addFavoriteFeeds($feed);
            }else{
                $user->removeFavoriteFeeds($feed);
            }
            try{
                $em->persist($user);
                $em->flush();

                return true;
            }catch(\Exception $e){
                return false;
            }
        }
        return false;
    }

    /**
     * Rate a feed.
     *
     * @param int $id
     * @param string $rating
     * @return bool|\Feed\Entity\Feed
     */
    public function rate($id, $rating)
    {
        $em = $this->getEntityManager();
        $feed = $this->getFeedRepository()->find($id);
        $user = $this->getAuthService()->getIdentity();
        $rating = ($rating == 'up') ? 1 : 0;
        $rateEntity = $em->getRepository('Feed\Entity\Rating')->findOneBy(array(
                'user' => $user->getId(),
                'feed' => $feed->getId()
            )
        );
        try {
            if ($rateEntity) {
                // check if the rating happens to be the same with the existing one, if so exit
                if ($rateEntity->getRating() != $rating) {
                    $rateEntity->setRating($rating);
                }
                $newRating = ($rating > 0) ? 2 : -2;
            } else {
                $rateEntity = new \Feed\Entity\Rating($user, $feed, $rating);
                $newRating = ($rating > 0) ? 1 : -1;
            }
            $feed->setRating($feed->getRating() + $newRating);
            $em->persist($rateEntity);
            $em->persist($feed);
            $em->flush();

            return $feed;
        } catch (\Exception $e) {
            return false;
        }
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