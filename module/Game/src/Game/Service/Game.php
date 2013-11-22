<?php
/**
 * User: Josh
 * Date: 9/10/2013
 * Time: 1:04 μμ
 */

namespace Game\Service;


use Game\Controller\GameController;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Form\Form;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManager;

class Game implements ServiceManagerAwareInterface
{
    const ERROR_FAIL_GAME_CONNECT = "Something went wrong when trying to connect with the game.";
    const ERROR_ALREADY_FOLLOWING_GAME = "You are already following this game.";
    const ERROR_ALREADY_NOT_FOLLOWING_GAME = "You are already not following this game.";

    const MESSAGE_UNFOLLOW = 'You are not following %s anymore.';
    const MESSAGE_FOLLOW_SUCCESS = "You are now following %s";
    const MESSAGE_FOLLOW = 'You are now following %s.';

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $gameRepository;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $feedRepository;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $accountRepository;

    public function connect($gameId,$type){
        /**
         * @var $game \Game\Entity\Game
         */
        $game = $this->getGameRepository()->find($gameId);
        $user = $this->getAccountRepository()->find($this->getAuthService()->getIdentity()->getId());
        $result = array("success" => 0, "message" => "");
        $em = $this->getEntityManager();
        try {
            $followers = $game->getFollowers();
            if ($type == 'follow') {
                // check if the user already follows the game
                if (!$followers->contains($user)) {
                    $game->addFollowers(array($user));
                    $game->setFollowersCount($game->getFollowersCount()+1);
                    $result["message"] = sprintf($this->getTranslator()->translate(self::MESSAGE_FOLLOW), $game->getName());
                } else {
                    $result["message"] = $this->getTranslator()->translate(self::ERROR_ALREADY_FOLLOWING_GAME);
                    return $result;
                }
            } else if ($type == 'unfollow') {
                // check if the user follows the game in order to unfollow it
                if ($followers->contains($user)) {
                    $game->removeFollowers(array($user));
                    $game->setFollowersCount($game->getFollowersCount()-1);
                    $result["message"] = sprintf($this->getTranslator()->translate(self::MESSAGE_UNFOLLOW), $game->getName());
                } else {
                    $result["message"] = $this->getTranslator()->translate(self::ERROR_ALREADY_NOT_FOLLOWING_GAME);
                    return $result;
                }
            }
            $em->persist($game);
            $em->flush();
            $result["success"] = 1;
        } catch (\Exception $e) {
            $result["message"] = $e->getMessage();
        }
        return $result;
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
     * @return Game
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }

    /**
     * Sets the doctrine entity manager.
     *
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns the doctrine entity manager.
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
     * Sets the service manager.
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Returns the service manager.
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Returns a zend translator instance.
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            $this->setTranslator($this->getServiceManager()->get('translator'));
        }
        return $this->translator;
    }

    /**
     * Sets the zend translator instance.
     *
     * @param $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

}