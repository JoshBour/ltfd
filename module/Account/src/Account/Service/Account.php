<?php
namespace Account\Service;

use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use  Zend\Form\Form;
use Doctrine\ORM\EntityManager;


class Account implements ServiceManagerAwareInterface{

    /**
     * @var Form
     */
    private $loginForm;

    /**
     * @var Form
     */
    private $registerForm;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * var EntityManager
     */
    private $entityManager;

    public function register($data){
        $form = $this->getRegisterForm();
        $account = new \Account\Entity\Account();

        $form->bind($account);
        $form->setData($data);
        if(!$form->isValid()){
            return false;
        }

        $em = $this->getEntityManager();
        $account->setRegisterDate(date("Y-m-d H:i:s", time()));
        $account->setIsActivated(0);
        $account->setIp($_SERVER['REMOTE_ADDR']);
        $account->setPassword(\Account\Entity\Account::getHashedPassword($account->getPassword()));
        try{
            $em->persist($account);
            $em->flush();
            mkdir(PUBLIC_PATH . '/images/users/' . $account->getId());
            return $account;
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * Return the account login form
     *
     * @return Form
     */
    public function getLoginForm()
    {
        if (null == $this->loginForm)
            $this->setLoginForm($this->getServiceManager()->get('account_login_form'));
        return $this->loginForm;
    }

    /**
     * Set the account login form
     *
     * @param Form $loginForm
     * @return Account
     */
    public function setLoginForm(Form $loginForm)
    {
        $this->loginForm = $loginForm;
        return $this;
    }

    /**
     * Return the account register form
     *
     * @return Form
     */
    public function getRegisterForm()
    {
        if (null == $this->registerForm)
            $this->setRegisterForm($this->getServiceManager()->get('account_register_form'));
        return $this->registerForm;
    }

    /**
     * Set the account register form
     *
     * @param Form $registerForm
     * @return Account
     */
    public function setRegisterForm(Form $registerForm)
    {
        $this->registerForm = $registerForm;
        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return Account
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * getAuthService
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        if (null === $this->authService) {
            $this->authService = $this->getServiceManager()->get('auth_service');
        }
        return $this->authService;
    }

    /**
     * setAuthenticationService
     *
     * @param AuthenticationService $authService
     * @return Account
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }

    /**
     * Retrieve the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        if(null === $this->entityManager){
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        }
        return $this->entityManager;
    }

    /**
     * Set the doctrine entity manager
     *
     * @param EntityManager $entityManager
     * @return Account
     */
    public function setEntityManager(EntityManager $entityManager){
        $this->entityManager = $entityManager;
        return $this;
    }

}
