<?php
namespace Account;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use \Zend\InputFilter\InputFilter;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class Module
{
	public function onBootstrap(MvcEvent $e)
	{
		$eventManager        = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);	
	}
	
	public function getControllerConfig(){
		return array(
            'factories' => array(
                'Account\Controller\Account' => function($sm){
                    $serviceLocator = $sm->getServiceLocator();
                    $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
                    $controller = new Controller\AccountController();
                    $controller->setTranslator($serviceLocator->get('translator'))
                               ->setAuthenticationService($serviceLocator->get('auth_service'))
                               ->setAuthStorage($serviceLocator->get('authStorage'))
                               ->setEntityManager($entityManager)
                               ->setRegisterForm($serviceLocator->get('account_register_form'))
                               ->setLoginForm($serviceLocator->get('account_login_form'))
                               ->setAccountService($serviceLocator->get('account_service'));


                    return $controller;
                }
            ),
            'aliases' => array(
                'account_controller' => 'Account\Controller\Account'
            )
		);
	}

    public function getControllerPluginConfig(){
        return array(
            'factories' => array(
                'user' => function($sm){
                    $plugin = new Plugin\ActiveAccount();
                    $plugin->setServiceManager($sm->getServiceLocator());
                    return $plugin;
                }
            )
        );
    }

    public function getViewHelperConfig(){
        return array(
          'factories' => array(
              'user' => function($sm){
                  $helper = new View\Helper\User();
                  $helper->setServiceManager($sm->getServiceLocator());
                  return $helper;
              }
          )
        );
    }
	
	public function getServiceConfig(){
		return array(
			'factories' => array(
				'authStorage' => function($sm){
					return new \Account\Model\AuthStorage();
				},
                'account_service' => function($sm){
                  return new \Account\Service\Account();
                },
				'Zend\Authentication\AuthenticationService' => function($sm){
						$authService = $sm->get('doctrine.authenticationservice.orm_default');
						$authService->setStorage($sm->get('AuthStorage'));
						return $authService;
				}
				,
				'account_login_form' => function($sm){
					$entityManager = $sm->get('Doctrine\ORM\EntityManager');
                    $fieldset = new Form\LoginFieldset();
                    $form = new Form\LoginForm();

                    $fieldset->setUseAsBaseFieldset(true)
                             ->setTranslator($sm->get('translator'))
                             ->setHydrator(new DoctrineHydrator($entityManager, 'Entity\Account'))
                             ->setObject(new Entity\Account);

                    $form->add($fieldset)
                         ->setInputFilter(new InputFilter())
                         ->setHydrator(new DoctrineHydrator($entityManager, 'Entity\Account'));

					return $form;
				},
				'account_register_form' => function($sm){
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
					$fieldset = new Form\RegisterFieldset();
                    $form = new Form\RegisterForm();

                    $fieldset->setAccountRepository($entityManager->getRepository('Entity\Account'))
                             ->setUseAsBaseFieldset(true)
                             ->setTranslator($sm->get('translator'))
                             ->setHydrator(new DoctrineHydrator($entityManager, 'Entity\Account'))
                             ->setObject(new Entity\Account);

					$form->add($fieldset)
                         ->setInputFilter(new InputFilter())
                         ->setHydrator(new DoctrineHydrator($entityManager, 'Entity\Account'));
					return $form;
				},
			),
			'aliases' => array(
				'auth_service' => 'Zend\Authentication\AuthenticationService'
			)
		);
	}
	
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
	
	public function getAutoloaderConfig()
	{
		return array(
				'Zend\Loader\ClassMapAutoloader' => array(
						__DIR__ . '/autoload_classmap.php',
				),
				'Zend\Loader\StandardAutoloader' => array(
						'namespaces' => array(
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
						),
				),
		);
	}	
}
