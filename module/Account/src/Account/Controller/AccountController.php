<?php
namespace Account\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Account\Entity\Account;

class AccountController extends AbstractActionController
{
    const CONTROLLER_NAME = 'account_controller';

    const ROUTE_LOGIN = 'login';
    const ROUTE_REGISTER = 'register';
    const ROUTE_HOMEPAGE = 'home';

    /**
     * @var Form
     */
    private $loginForm = null;

    /**
     * @var Form
     */
    private $registerForm = null;

    /**
     * @var \Account\Service\Account
     */
    private $accountService = null;

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
        if (!$user = $this->identity()) {
            $entity = new Account();
            $loginForm = $this->getLoginForm();
            $request = $this->getRequest();
            $loginForm->bind($entity);
            if ($request->isPost()) {
                $data = $request->getPost();
                $loginForm->setData($data);
                if ($loginForm->isValid()) {
                    return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate',
                        'username' => $entity->getUsername(),
                        'password' => $entity->getPassword(),
                        'remember' => $data['account']['remember']));
                } else {
                    $this->flashMessenger()->addMessage($loginForm->getMessages());
                }
            }
            return new ViewModel(array(
                'form' => $this->getLoginForm(),
                'bodyClass' => 'connectPage'
            ));
        } else {
            return $this->redirect()->toRoute(self::ROUTE_HOMEPAGE);
        }
    }

    public function logoutAction()
    {
        if ($this->identity()) {
            $this->getAuthStorage()->forgetMe();
            $this->getAuthenticationService()->clearIdentity();
        }
        return $this->redirect()->toRoute(self::ROUTE_LOGIN);
    }

    public function registerAction()
    {
        if (!$this->identity()) {
            $service = $this->getAccountService();
            $request = $this->getRequest();
            $form = $this->getRegisterForm();
            if ($request->isPost()) {
                $account = $service->register($request->getPost());
                if ($account) {
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Your account has been created. Make sure to check your emails for the validation link.'));

                    return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate',
                            'username' => $account->getUsername(),
                            'password' => $account->getPassword())
                    );
                }
            }
            return new ViewModel(array(
                'form' => $form,
                'bodyClass' => 'connectPage'
            ));
        } else {
            return $this->redirect()->toRoute(self::ROUTE_HOMEPAGE);
        }
    }

    public function authenticateAction()
    {
        $authService = $this->getAuthenticationService();
        $adapter = $authService->getAdapter();

        $remember = $this->params()->fromRoute('remember', 1);
        $username = $this->params()->fromRoute('username');
        $password = $this->params()->fromRoute('password');

        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $authResult = $authService->authenticate();
        if ($authResult->isValid()) {
            if ($remember == 1) {
                $this->getAuthStorage()->setRememberMe(1);
                $authService->setStorage($this->getAuthStorage());
            }
            $authService->getStorage()->write($authResult->getIdentity());
        } else {
            $this->flashMessenger()->addMessage($this->getTranslator()->translate('The username/password combination is invalid.'));
            return $this->redirect()->toRoute(self::ROUTE_LOGIN);
        }

        return $this->redirect()->toRoute(self::ROUTE_HOMEPAGE);
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

    /**
     * Retrieve the account service
     *
     * @return \Account\Service\Account
     */
    public function getAccountService()
    {
        if (null === $this->accountService)
            $this->setAccountService($this->getServiceLocator()->get('account_service'));
        return $this->accountService;
    }

    /**
     * Set the account service
     *
     * @param \Account\Service\Account $accountService
     * @return AccountController
     */
    public function setAccountService($accountService)
    {
        $this->accountService = $accountService;
        return $this;
    }

    /**
     * Retrieve the account login form
     *
     * @return Form
     */
    public function getLoginForm()
    {
        if (!$this->loginForm) {
            $this->setLoginForm($this->getServiceLocator()->get('account_login_form'));
        }
        return $this->loginForm;
    }

    /**
     * Set the account login form
     *
     * @param Form $loginForm
     */
    public function setLoginForm($loginForm)
    {
        $this->loginForm = $loginForm;
    }

    /**
     * Retrieve the account register form
     *
     * @return Form
     */
    public function getRegisterForm()
    {
        if (!$this->registerForm) {
            $this->setRegisterForm($this->getServiceLocator()->get('account_register_form'));
        }
        return $this->registerForm;
    }

    /**
     * Set the account register form
     *
     * @param Form $registerForm
     */
    public function setRegisterForm($registerForm)
    {
        $this->registerForm = $registerForm;
    }

    /**
     * Retrieve the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
        }
        return $this->entityManager;
    }

    /**
     * Set the doctrine entity manager
     *
     * @param $em
     */
    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    /**
     * Retrieve the translator
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            $this->setTranslator($this->getServiceLocator()->get('translator'));
        }
        return $this->translator;
    }

    /**
     * Set the translator
     *
     * @param $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * Retrieve the authentication service
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (!$this->authService) {
            $this->setAuthenticationService($this->getServiceLocator()->get('auth_service'));
        }
        return $this->authService;
    }

    /**
     * Set the authentication service
     *
     * @param $authService
     */
    public function setAuthenticationService($authService)
    {
        $this->authService = $authService;
    }

    /**
     * Retrieve the auth storage
     *
     * @return \Account\Model\AuthStorage
     */
    public function getAuthStorage()
    {
        if (!$this->authStorage) {
            $this->setAuthStorage($this->getServiceLocator()->get('authStorage'));
        }
        return $this->authStorage;
    }

    /**
     * Set the auth storage
     *
     * @param $authStorage
     */
    public function setAuthStorage($authStorage)
    {
        $this->authStorage = $authStorage;
    }
}
