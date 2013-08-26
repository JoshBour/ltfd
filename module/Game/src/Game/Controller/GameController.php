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
	
	public function profileAction(){
		return new ViewModel();
	}
	
	public function feedsAction(){
		return new ViewModel();
	}
	
	public function connectAction(){
		return new JsonModel();
	}

    public function rateAction(){
        return new JsonModel();
    }

    public function listAction(){
        $searchForm = $this->getServiceLocator()->get('game_search_form');
        $games = $this->getEntityManager()->getRepository('Game\Entity\Game')->findAll();
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

            if($name != 'allgames'){
                $games = $this->getEntityManager()->getRepository('Game\Entity\Game')->searchByName($name);
            }else{
                $games = $this->getEntityManager()->getRepository('Game\Entity\Game')->findAll();
            }
            $viewModel->setVariable('games',$games);
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
     * @return Doctrine\ORM\EntityManager
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
     * @return Zend\I18n\Translator\Translator
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
	
	
}
