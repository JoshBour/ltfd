<?php
namespace User\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

class UserController extends AbstractActionController
{
	/**
	 * @var Form
	 */
	private $loginForm = null;
	
	/**
	 * @var Form
	 */	
	private $registerForm = null;
	
	/**
	 * @var AuthenticationService
	 */
	private $authService = null;
	
	/**
	 * @var \User\Model\AuthStorage
	 */
	 private $authStorage = null;
	
    public function profileAction()
    {
        return new ViewModel();
    }
	
    public function loginAction()
    {
    	if(!$user = $this->identity()){
    		$errors = array();
    		$loginForm = $this->getLoginForm();
			if($this->getRequest()->isPost()){
				$loginForm->setData($this->getRequest()->getPost());
				if($loginForm->isValid()){
					$authService = $this->getAuthenticationService();
					$data = $loginForm->getData();
					
					$adapter = $authService->getAdapter();
					$adapter->setIdentityValue($data['username']);
					$adapter->setCredentialValue($data['password']);
					$authResult = $authService->authenticate();
					if($authResult->isValid()){
						if($data['rememberme'] && ((int)$data['rememberme'] == 1)){
							$this->getAuthStorage()->setRememberMe(1);
							$authService->setStorage($this->getAuthStorage());
						}
						$authService->getStorage()->write($authResult->getIdentity());
						return $this->redirect()->toRoute('admin_main/films');
					}else{
						foreach($authResult->getMessages() as $message){
							echo '-' . $message . '<br />';
						}
					}
				}
				
			}	
	        return new ViewModel(array(
				'loginForm' => $this->getLoginForm()
			));
    	}else{
    		return $this->redirect()->toRoute('admin_main/films');
    	}
    }
    
    public function logoutAction()
    {
    	if($this->identity()){
    		$this->getAuthStorage()->forgetMe();
    		$this->getAuthenticationService()->clearIdentity();
    	}
    	return $this->redirect()->toRoute('account-login');
    }    
    
    public function registerAction()
    {
    	return new ViewModel(array(
			'registerForm' => $this->getRegisterForm()
		));
    }  
	
	public function getLoginForm(){
		if(!$this->loginForm){
			$this->setLoginForm($this->getServiceLocator()->get('user_login_form'));
		}
		return $this->loginForm;
	}  
	
	public function setLoginForm($loginForm){
		$this->loginForm = $loginForm;
	}
	
	public function getRegisterForm(){
		if(!$this->registerForm){
			$this->setRegisterForm($this->getServiceLocator()->get('user_register_form'));
		}
		return $this->registerForm;
	}  
	
	public function setRegisterForm($registerForm){
		$this->registerForm = $registerForm;
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
