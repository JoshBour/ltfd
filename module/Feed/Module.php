<?php
namespace User;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
	public function onBootstrap(MvcEvent $e)
	{
		$eventManager        = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);

		$translator = $e->getApplication()->getServiceManager()->get('translator');
	
		$sharedManager = $eventManager->getSharedManager();
		$sharedManager->attach(__NAMESPACE__,'dispatch',function($e){
			$controller = $e->getTarget();
			$controller->layout('layout/login');
		});
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
				'Zend\Loader\StandardAutoloader' => array(
						'namespaces' => array(
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
						),
				),
		);
	}
	
	public function getControllerConfig(){
		return array(
	        'invokables' => array(
	            'User\Controller\User' => 'User\Controller\UserController'
	        )
		);
	}
	
	public function getServiceConfig(){
		return array(
			'factories' => array(
				'authStorage' => function($sm){
					return new \User\Model\AuthStorage();
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
}
