<?php
namespace Account;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

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
	        'invokables' => array(
	            'Account\Controller\Account' => 'Account\Controller\AccountController'
	        )
		);
	}
	
	public function getServiceConfig(){
		return array(
			'factories' => array(
				'authStorage' => function($sm){
					return new \Account\Model\AuthStorage();
				},
				'Zend\Authentication\AuthenticationService' => function($sm){
						$authService = $sm->get('doctrine.authenticationservice.orm_default');
						$authService->setStorage($sm->get('AuthStorage'));
						return $authService;
				}
				,
				'user_login_form' => function($sm){
					$em = $sm->get('Doctrine\ORM\EntityManager');
					$form = new \User\Form\Login($em);
					$form->setInputFilter(new \User\Form\LoginFilter());
					return $form;
				},
				'user_register_form' => function($sm){
					$em = $sm->get('Doctrine\ORM\EntityManager');
					/**
					 * @var \Zend\Form\Fieldset
					 */
					$fieldset = new \User\Form\RegisterFieldset($em);
					$fieldset->setUseAsBaseFieldset(true);
					$form = new \User\Form\RegisterForm($em);
					$form->add($fieldset);
					return $form;
				}
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
