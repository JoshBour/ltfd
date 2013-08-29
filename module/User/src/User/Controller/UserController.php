<?php
namespace User\Controller;

use Zend\View\Model\JsonModel;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

class UserController extends AbstractActionController
{
	/**
	 * @var AuthenticationService
	 */
	private $authService = null;

	/**
	 * @var \User\Model\AuthStorage
	 */
	private $authStorage = null;

	public function profileAction(){
		return new ViewModel();
	}

	public function preferencesAction()
	{
		return new ViewModel(array(
            'bodyClass' => 'userPage'
        ));
	}

	public function detailsAction(){
        return new ViewModel(array(
            'bodyClass' => 'userPage'
        ));
	}

	public function gamesAction(){
        return new ViewModel(array(
            'bodyClass' => 'userPage'
        ));
	}

	public function followAction(){
		return new JsonModel();
	}

	public function unfollowAction(){
		return new JsonModel();
	}

	public function followersAction(){
        return new ViewModel(array(
            'bodyClass' => 'userPage'
        ));
	}

	public function feedsAction(){
        return new ViewModel(array(
            'bodyClass' => 'userPage'
        ));
	}
	 

	public function getAuthenticationService(){
		if(!$this->authService){
			$this->setAuthenticationService($this->getServiceLocator()->get('auth_service'));
		}
		return $this->authService;
	}

	public function setAuthenticationService($authService){
		$this->authService = $authService;
	}

	public function getAuthStorage(){
		if(!$this->authStorage){
			$this->setAuthStorage($this->getServiceLocator()->get('authStorage'));
		}
		return $this->authStorage;
	}

	public function setAuthStorage($authStorage){
		$this->authStorage = $authStorage;
	}
}
