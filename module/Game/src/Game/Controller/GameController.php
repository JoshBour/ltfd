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
        if($this->getRequest()->isXmlHttpRequest()){
            $em = $this->getEntityManager();
            $game = $em->getRepository('Game\Entity\Game')->findOneBy(array('name'=>$this->params('name')));
            $user = $em->getRepository('Account\Entity\Account')->find($this->identity()->getId());
            $success = 0;
            $message = '';

            $type = $this->params('type');
            try{
                $followers = $game->getFollowers();
                if($type == 'follow'){
                    if(!$followers->contains($user)){
                        $game->addFollowers(array($user));
                        $message = sprintf($this->getTranslator()->translate('You are now following %s.'),$game->getName());
                    }else{
                        return new JsonModel(array('success' => $success, 'message' => $this->getTranslator()->translate('You are already following this game.')));
                    }
                }else{
                    if($followers->contains($user)){
                        $game->removeFollowers(array($user));
                        $message = sprintf('You are not following %s anymore.',$game->getName());
                    }else{
                        return new JsonModel(array('success' => $success, 'message' => $this->getTranslator()->translate('You are already not following this game.')));
                    }
                }
                $em->persist($game);
                $em->flush();
                $success = 1;
            }catch(Exception $e){
                $message = $e->getMessage();
            }

            return new JsonModel(array('success' => $success, 'message' => $message, 'followers' => count($followers)));
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
        $games = $this->getEntityManager()->getRepository('Game\Entity\Game')->findBy(array(),array('name' => 'ASC'));
        $user = $this->getEntityManager()->getRepository('Account\Entity\Account')->find($this->identity()->getId());
        return new ViewModel(array(
            'user' => $user,
            'searchForm' => $searchForm,
            'bodyClass' => 'gameList',
            'games' => $games
        ));
    }

    public function searchAction(){
        if($this->getRequest()->isXmlHttpRequest()){
            $viewModel = new ViewModel();
            $name = $this->params('name', null);
            $user = $this->getEntityManager()->getRepository('Account\Entity\Account')->find($this->identity()->getId());

            $games = $this->getEntityManager()->getRepository('Game\Entity\Game')->searchByName($name);

            $viewModel->setVariables(array('games' => $games,'user' => $user));
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
	
	
}
