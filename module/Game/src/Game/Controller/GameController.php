<?php
namespace Game\Controller;

use Zend\View\Model\JsonModel;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GameController extends AbstractActionController
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;

	/**
	 * @var Zend\I18n\Translator\Translator
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
        return new ViewModel();
    }

    public function suggestAction(){
        return new ViewModel();
    }
	
	public function getEntityManager() {
		if (!$this -> entityManager) {
			$this -> setEntityManager($this -> getServiceLocator() -> get('Doctrine\ORM\EntityManager'));
		}
		return $this -> entityManager;
	}

	public function setEntityManager($em) {
		$this -> entityManager = $em;
	}

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
