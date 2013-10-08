<?php
namespace Game\Controller;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GameController extends AbstractActionController
{
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
        $game = $this->getGameRepository()->findOneBy(array('urlName' => $this->params('name')));
        $categoryName = $this->params()->fromRoute('category');
        $activeSort = $this->params()->fromRoute('sort', 'popular');
        $translator = $this->getTranslator();

        if ($categoryName == 'all' || $categoryName == 'random') {
            $category = $categoryName;
        } else if (empty($categoryName)) {
            $category = 'all';
        } else {
            $category = $em->getRepository('Game\Entity\Category')->findOneBy(array('name' => $categoryName));
        }
        // if the game or the category don't exist, throw a 404
        if (!$game || !$category) {
            $this->getResponse()->setStatusCode(404);
            return;
        } else {
            if ($category == 'all') {
                $activeCategory = 'all';
                $feeds = $em->getRepository('Feed\Entity\Feed')->findBySort($game->getId(),$activeSort);
            } else if ($category == 'random') {
                $activeCategory = 'random';
                $feeds = $em->getRepository('Feed\Entity\Feed')->findBySort($game->getId(),$activeSort);
            } else {
                $activeCategory = $category->getName();
                $feeds = $em->getRepository('Feed\Entity\Feed')->findBySort($game->getId(),$activeSort, $category->getId());
            }
            $sortOptions = array($translator->translate('popular'), $translator->translate('new'));

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
     * @return \Zend\Form\Form
     */
    public function getCommentForm()
    {
        if (!$this->commentForm) {
            $this->setCommentForm($this->getServiceLocator()->get('comment_form'));
        }
        return $this->commentForm;
    }

    public function setCommentForm($commentForm)
    {
        $this->commentForm = $commentForm;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
        }
        return $this->entityManager;
    }

    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    /**
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            $this->setTranslator($this->getServiceLocator()->get('translator'));
        }
        return $this->translator;
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getGameRepository()
    {
        if (!$this->gameRepository) {
            $this->setGameRepository($this->getEntityManager()->getRepository('Game\Entity\Game'));
        }
        return $this->gameRepository;
    }

    public function setGameRepository($gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }


}
