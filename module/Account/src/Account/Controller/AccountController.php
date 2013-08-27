<?php
namespace Account\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Account\Entity\Account;

class AccountController extends AbstractActionController
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
	 * @var \Account\Model\AuthStorage
	 */
	 private $authStorage = null;
	
    public function loginAction()
    {
    	if(!$user = $this->identity()){
            $entity = new Account();
    		$loginForm = $this->getLoginForm();
            $loginForm->bind($entity);
			if($this->getRequest()->isPost()){
                $data = $this->getRequest()->getPost();
                $loginForm->setData($data);
				if($loginForm->isValid()){
					$authService = $this->getAuthenticationService();
                    $adapter = $authService->getAdapter();
                    $adapter->setIdentityValue($entity->getUsername());
                    $adapter->setCredentialValue($entity->getPassword());
                    $authResult = $authService->authenticate();
                    if($authResult->isValid()){
                        if($data['remember'] && ((int)$data['remember'] == 1)){
                            $this->getAuthStorage()->setRememberMe(1);
                            $authService->setStorage($this->getAuthStorage());
                        }
                        $authService->getStorage()->write($authResult->getIdentity());
                        return $this->redirect()->toRoute('home');
                    }else{
                        foreach($authResult->getMessages() as $message){
                            echo '-' . $message . '<br />';
                        }
                    }
				}else{
                    echo "there was something wrong with the data";
                }
				
			}	
	        return new ViewModel(array(
				'form' => $this->getLoginForm(),
                'bodyClass' => 'loginPage'
			));
    	}else{
    		return $this->redirect()->toRoute('home');
    	}
    }
    
    public function logoutAction()
    {
    	if($this->identity()){
    		$this->getAuthStorage()->forgetMe();
    		$this->getAuthenticationService()->clearIdentity();
    	}
    	return $this->redirect()->toRoute('login');
    }    
    
    public function registerAction()
    {
    	return new ViewModel(array(
			'registerForm' => $this->getRegisterForm()
		));
    }  
    
    public function deleteAction()
    {
    	return new ViewModel();
    }

    public function reportAction()
    {
    	return new ViewModel();
    }  

    public function verifyAction()
    {
    	return new ViewModel();
    }    
	
	public function getLoginForm(){
		if(!$this->loginForm){
			$this->setLoginForm($this->getServiceLocator()->get('account_login_form'));
		}
		return $this->loginForm;
	}  
	
	public function setLoginForm($loginForm){
		$this->loginForm = $loginForm;
	}
	
	public function getRegisterForm(){
		if(!$this->registerForm){
			$this->setRegisterForm($this->getServiceLocator()->get('account_register_form'));
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
