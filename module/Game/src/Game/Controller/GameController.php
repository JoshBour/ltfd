<?php
namespace Game\Controller;

use Feed\Service\Game;
use Feed\Entity\Feed;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;

class GameController extends AbstractActionController
{
    const ROUTE_HOMEPAGE = 'home';
    const ROUTE_GAMES_LIST = 'games';

    const ERROR_INVALID_CATEGORY = "The category is invalid.";
    const ERROR_CATEGORY_NOT_FOUND = "The category was not found or is invalid.";

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * @var \Game\Repository\GameRepository
     */
    private $gameRepository;

    /**
     * @var \Feed\Repository\FeedRepository
     */
    private $feedRepository;

    /**
     * @var \Feed\Service\Feed
     */
    private $feedService;

    /**
     * @var \Game\Service\Game
     */
    private $gameService;

    public function profileAction()
    {
        return new ViewModel();
    }

    public function feedsAction()
    {
        $gameName = $this->params()->fromRoute('name', null);
        $category = $this->params()->fromRoute('category', 'feeds');
        $game = $this->getGameRepository()->findOneBy(array('urlName' => $gameName));
        $page = $this->params()->fromRoute('page');
        $index = $this->params()->fromRoute('index');
        $translator = $this->getTranslator();
        $isHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $viewModel = new ViewModel();
        $maxResults = 35;
        if ($isHttpRequest) {
            $viewModel->setTemplate('Game/Game/feeds.ajax.phtml');
            $viewModel->setTerminal(true);
        }
        // check if the game exists or the name is invalid
        if (!$game || null === $gameName) {
            $this->flashMessenger()->addMessage($translator->translate(self::ERROR_CATEGORY_NOT_FOUND));
            $this->redirect()->toRoute(self::ROUTE_GAMES_LIST);
        }

        // if the category doesn't exist, throw a 404
        if (!$category || !in_array($category, Feed::$feedTypes)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        if ($category == 'feeds') {
        } else {
        }

        $viewModel->setVariables(array(
            'game' => $game,
           # 'feeds' => $feeds,
            'category' => $category,
            'bodyClass' => 'gameFeeds',
            'index' => $index
        ));


        return $viewModel;

    }

    public function connectAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $gameId = $this->params()->fromRoute('id');
            $type = $this->params()->fromRoute('type');
            $result = $this->getGameService()->connect($gameId, $type);

            return new JsonModel($result);
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function rateAction()
    {
        return new JsonModel();
    }

    public function listAction()
    {
        $searchForm = $this->getServiceLocator()->get('game_search_form');
        $games = $this->getGameRepository()->findBy(array(), array('name' => 'ASC'));

        return new ViewModel(array(
            'searchForm' => $searchForm,
            'bodyClass' => 'gameList',
            'games' => $games
        ));
    }

    public function searchAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel = new ViewModel();
            $name = $this->params()->fromRoute('name', null);
            $games = $this->getGameRepository()->searchByName($name);

            $viewModel->setVariables(array('games' => $games));
            $viewModel->setTerminal(true);
            return $viewModel;
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function suggestAction()
    {
        return new ViewModel();
    }

    /**
     * Retrieve the doctrine entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
        }
        return $this->entityManager;
    }


    /**
     * Set the doctrine entity manager.
     *
     * @param $em
     */
    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    /**
     * Get the feed service.
     *
     * @return \Feed\Service\Feed
     */
    public function getFeedService()
    {
        if (null === $this->feedService) {
            $this->setFeedService($this->getServiceLocator()->get('feed_service'));
        }
        return $this->feedService;
    }

    /**
     * Set the feed service.
     *
     * @param $feedService
     */
    public function setFeedService($feedService)
    {
        $this->feedService = $feedService;
    }

    /**
     * Get the game service.
     *
     * @return \Game\Service\Game
     */
    public function getGameService()
    {
        if (null === $this->gameService) {
            $this->setGameService($this->getServiceLocator()->get('game_service'));
        }
        return $this->gameService;
    }

    /**
     * Set the game service.
     *
     * @param $gameService
     */
    public function setGameService($gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Get a zend translator instance.
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            $this->setTranslator($this->getServiceLocator()->get('translator'));
        }
        return $this->translator;
    }

    /**
     * Set the zend translator instance.
     *
     * @param $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get the game repository.
     *
     * @return \Game\Repository\GameRepository
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
