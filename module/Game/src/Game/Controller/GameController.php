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
	
	public function viewAction(){
		return new ViewModel();
	}
	
	public function feedsAction(){
		return new ViewModel();
	}
	
	public function followAction(){
		return new JsonModel();
	}
	
	public function unfollowAction(){
		return new JsonModel();
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
