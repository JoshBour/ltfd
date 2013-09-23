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
	
	public function profileAction(){
		return new ViewModel();
	}
	
	public function feedsAction(){
        $em = $this->getEntityManager();
        $game = $this->getGameRepository()->findOneBy(array('urlName' => $this->params('name')));
        $category = $em->getRepository('Game\Entity\Category')->findOneBy(array('name'=>$this->params('category','all')));

        // if the game or the category don't exist, throw a 404
        if(!$game || !$category){
            $this->getResponse()->setStatusCode(404);
            return;
        }else{
            $feeds = $em->getRepository('Feed\Entity\Feed')->findBy(array('game' => $game->getId(),'category' => $category->getId()), array(), 10, 0);
            $sortOptions = array('popular','new','all time');
            $activeSort = $this->params('sort','popular');

            return new ViewModel(array(
                'game' => $game,
                'sortOptions' => $sortOptions,
                'activeSort' => $activeSort,
                'feeds' => $feeds,
                'activeCategory' => $category->getName(),
                'bodyClass' => 'gameFeeds',
                'form' => $this->getCommentForm()
            ));
        }
	}
	
	public function connectAction(){
        if($this->getRequest()->isXmlHttpRequest()){
            $em = $this->getEntityManager();
            $game = $this->getGameRepository()->find($this->params('id'));
            $type = $this->params('type');
            $user = $this->user();
            $jsonModel = new JsonModel();
            $success = 0;
            $message = '';
            if($game){
                try{
                    $followers = $game->getFollowers();
                    if($type == 'follow'){
                        // check if the user already follows the game
                        if(!$followers->contains($user)){
                            $game->addFollowers(array($user));
                            $message = sprintf($this->getTranslator()->translate('You are now following %s.'),$game->getName());
                        }else{
                            return new JsonModel(array('success' => $success, 'message' => $this->getTranslator()->translate('You are already following this game.')));
                        }
                    }else if($type == 'unfollow'){
                        // check if the user follows the game in order to unfollow it
                        if($followers->contains($user)){
                            $game->removeFollowers(array($user));
                            $message = sprintf($this->getTranslator()->translate('You are not following %s anymore.'),$game->getName());
                        }else{
                            return new JsonModel(array('success' => $success, 'message' => $this->getTranslator()->translate('You are already not following this game.')));
                        }
                    }else{
                        $this->getResponse()->setStatusCode(404);
                        return;
                    }
                    $em->persist($game);
                    $em->flush();
                    $success = 1;
                }catch(Exception $e){
                    $message = $e->getMessage();
                }
                $jsonModel->setVariable('followers',count($followers));
            }else{
                $message = $this->translator->translate("Something went wrong when trying to connect with the game.");
            }
            $jsonModel->setVariables(array(
                'message' => $message,
                'success' => $success
            ));
            return $jsonModel;
        }else{
            $this->getResponse()->setStatusCode(404);
            return;
        }
	}

    public function rateAction(){
        return new JsonModel();
    }

    public function listAction(){
        $searchForm = $this->getServiceLocator()->get('game_search_form');
        $games = $this->getGameRepository()->findBy(array(),array('name' => 'ASC'));

        return new ViewModel(array(
            'searchForm' => $searchForm,
            'bodyClass' => 'gameList',
            'games' => $games
        ));
    }

    public function searchAction(){
        if($this->getRequest()->isXmlHttpRequest()){
            $viewModel = new ViewModel();
            $name = $this->params('name', null);
            $games = $this->getGameRepository()->searchByName($name);

            $viewModel->setVariables(array('games' => $games));
            $viewModel->setTerminal(true);
            return $viewModel;
        }else{
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function suggestAction(){
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
    public function getEntityManager() {
		if (!$this -> entityManager) {
			$this -> setEntityManager($this -> getServiceLocator() -> get('Doctrine\ORM\EntityManager'));
		}
		return $this -> entityManager;
	}

	public function setEntityManager($em) {
		$this -> entityManager = $em;
	}

    /**
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator() {
		if (!$this -> translator) {
			$this -> setTranslator($this -> getServiceLocator() -> get('translator'));
		}
		return $this -> translator;
	}

	public function setTranslator($translator) {
		$this -> translator = $translator;
	}

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getGameRepository(){
        if(!$this->gameRepository){
            $this->setGameRepository($this->getEntityManager()->getRepository('Game\Entity\Game'));
        }
        return $this->gameRepository;
    }

    public function setGameRepository($gameRepository){
        $this->gameRepository = $gameRepository;
    }
	
	
}
