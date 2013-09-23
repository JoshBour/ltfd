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

    public function getControllerPluginConfig(){
        return array(
            'factories' => array(
                'user' => function($sm){
                    $plugin = new \Account\Plugin\ActiveAccount();
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
                  $helper = new \Account\View\Helper\User();
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
				'Zend\Authentication\AuthenticationService' => function($sm){
						$authService = $sm->get('doctrine.authenticationservice.orm_default');
						$authService->setStorage($sm->get('AuthStorage'));
						return $authService;
				}
				,
				'account_login_form' => function($sm){
					$em = $sm->get('Doctrine\ORM\EntityManager');
                    $fieldset = new \Account\Form\LoginFieldset($sm);
                    $fieldset->setUseAsBaseFieldset(true);
                    $form = new \Account\Form\LoginForm($em);
                    $form->add($fieldset);
					return $form;
				},
				'account_register_form' => function($sm){
					$em = $sm->get('Doctrine\ORM\EntityManager');
					$fieldset = new \Account\Form\RegisterFieldset($sm);
					$fieldset->setUseAsBaseFieldset(true);
					$form = new \Account\Form\RegisterForm($em);
					$form->add($fieldset);
					return $form;
				},
                'Account\Entity\Account' => function($sm){
                    return new Entity\Account();
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
