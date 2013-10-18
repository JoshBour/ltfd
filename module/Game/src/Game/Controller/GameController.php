<?php
namespace Game\Controller;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;

class GameController extends AbstractActionController
{
    const ROUTE_HOMEPAGE = 'home';
    const ROUTE_GAMES_LIST = 'games';


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $gameRepository;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $feedRepository;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $categoryRepository;

    /**
     * @var \Zend\Form\Form
     */
    private $commentForm;

    public function profileAction()
    {
        return new ViewModel();
    }

    public function feedsAction()
    {
        $em = $this->getEntityManager();
        $gameName = $this->params()->fromRoute('name',null);
        $game = $this->getGameRepository()->findOneBy(array('urlName' => $gameName));
        $categoryName = $this->params()->fromRoute('category');
        $activeSort = $this->params()->fromRoute('sort');
        $translator = $this->getTranslator();
        $sortOptions = array($translator->translate('popular'), $translator->translate('new'));

        // check if the game exists or the name is invalid
        if(!$game || null === $gameName){
            $this->redirect()->toRoute(self::ROUTE_GAMES_LIST);
        }

        if ($categoryName == 'all' || $categoryName == 'random') {
            $category = $categoryName;
        }  else {
            $category = $em->getRepository('Game\Entity\Category')->findOneBy(array('name' => $categoryName));
        }

        // if the category doesn't exist or the sorting is invalid, throw a 404
        if (!$category || !in_array($activeSort,$sortOptions)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($category == 'all') {
            $activeCategory = 'all';
            $feeds = $this->getFeedRepository()->findBySort($game->getId(),$activeSort);
        } else if ($category == 'random') {
            $activeCategory = 'random';
            $feeds = $this->getFeedRepository()->findBySort($game->getId(),$activeSort);
        } else {
            $activeCategory = $category->getName();
            $feeds = $this->getFeedRepository()->findBySort($game->getId(),$activeSort, $category->getId());
        }

        return new ViewModel(array(
            'game' => $game,
            'sortOptions' => $sortOptions,
            'activeSort' => $activeSort,
            'feeds' => $feeds,
            'activeCategory' => $activeCategory,
            'bodyClass' => 'gameFeeds',
            'form' => $this->getCommentForm()
        ));

    }

    public function connectAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $em = $this->getEntityManager();
            $game = $this->getGameRepository()->find($this->params('id'));
            $type = $this->params('type');
            $user = $this->user();
            $jsonModel = new JsonModel();
            $success = 0;
            $message = '';
            if ($game) {
                try {
                    $followers = $game->getFollowers();
                    if ($type == 'follow') {
                        // check if the user already follows the game
                        if (!$followers->contains($user)) {
                            $game->addFollowers(array($user));
                            $message = sprintf($this->getTranslator()->translate('You are now following %s.'), $game->getName());
                        } else {
                            return new JsonModel(array('success' => $success, 'message' => $this->getTranslator()->translate('You are already following this game.')));
                        }
                    } else if ($type == 'unfollow') {
                        // check if the user follows the game in order to unfollow it
                        if ($followers->contains($user)) {
                            $game->removeFollowers(array($user));
                            $message = sprintf($this->getTranslator()->translate('You are not following %s anymore.'), $game->getName());
                        } else {
                            return new JsonModel(array('success' => $success, 'message' => $this->getTranslator()->translate('You are already not following this game.')));
                        }
                    } else {
                        $this->getResponse()->setStatusCode(404);
                        return;
                    }
                    $em->persist($game);
                    $em->flush();
                    $success = 1;
                } catch (Exception $e) {
                    $message = $e->getMessage();
                }
                $jsonModel->setVariable('followers', count($followers));
            } else {
                $message = $this->translator->translate("Something went wrong when trying to connect with the game.");
            }
            $jsonModel->setVariables(array(
                'message' => $message,
                'success' => $success
            ));
            return $jsonModel;
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

    public function getGameCategoriesAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $gameId = $this->params()->fromQuery('gameId');
            $success = 0;
            $jsonModel = new JsonModel();
            if (!empty($gameId)) {
                $game = $this->getGameRepository()->find($gameId);
                if ($game) {
                    $categories = array();
                    foreach ($game->getCategories() as $category) {
                        $categories[$category->getId()] = ucwords($category->getName());
                    }
                    $jsonModel->setVariable('categories', $categories);
                    $success = 1;
                }
            }
            $jsonModel->setVariable('success', $success);
            return $jsonModel;
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function searchAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel = new ViewModel();
            $name = $this->params('name', null);
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
     * Get the comment post form.
     *
     * @return Form
     */
    public function getCommentForm()
    {
        if (!$this->commentForm) {
            $this->setCommentForm($this->getServiceLocator()->get('comment_form'));
        }
        return $this->commentForm;
    }

    /**
     * Set the comment post form.
     *
     * @param Form $commentForm
     */
    public function setCommentForm($commentForm)
    {
        $this->commentForm = $commentForm;
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
     * Get the category repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getCategoryRepository()
    {
        if (!$this->categoryRepository) {
            $this->setCategoryRepository($this->getEntityManager()->getRepository('Game\Entity\Category'));
        }
        return $this->categoryRepository;
    }

    /**
     * Set the category repository.
     *
     * @param $categoryRepository
     */
    public function setCategoryRepository($categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
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


}
