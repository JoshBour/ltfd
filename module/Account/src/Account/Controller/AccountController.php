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

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

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
                    $this->loginUser($entity->getUsername(),$entity->getPassword(),$data['account']['remember']);
                    return $this->redirect()->toRoute('home');
				}else{
                    echo "there was something wrong with the data";
                }

			}
	        return new ViewModel(array(
				'form' => $this->getLoginForm(),
                'bodyClass' => 'connectPage'
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
        if(!$this->identity()){
            $entity = new Account();
            $request = $this->getRequest();
            $form = $this->getRegisterForm();
            $form->bind($entity);
            if($request->isPost()){
                $data = $request->getPost();
                $form->setData($data);
                if($form->isValid()){
                    $em = $this->getEntityManager();
                    $entity->setRegisterDate(date("Y-m-d H:i:s", time()));
                    $entity->setIsActivated(0);
                    $entity->setIp($_SERVER['REMOTE_ADDR']);
                    $entity->setPassword(crypt($entity->getPassword().'leetfeedpenbour'));
                    $em->persist($entity);
                    $em->flush();
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Your account has been created. Make sure to check your emails for the validation link.'));
                    $this->loginUser($entity->getUsername(),$data['account']['password']);
                    return $this->redirect()->toRoute('home');
                }
            }
            return new ViewModel(array(
                'form' => $form,
                'bodyClass' => 'connectPage'
            ));
        }else{
            return $this->redirect()->toRoute('home');
        }
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

    private function loginUser($username,$password,$remember = 1){
        $authService = $this->getAuthenticationService();
        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $authResult = $authService->authenticate();
        if($authResult->isValid()){
            if($remember == 1){
                $this->getAuthStorage()->setRememberMe(1);
                $authService->setStorage($this->getAuthStorage());
            }
            $authService->getStorage()->write($authResult->getIdentity());
        }else{
            foreach($authResult->getMessages() as $message){
                echo '-' . $message . '<br />';
            }
        }
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
