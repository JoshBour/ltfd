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
            'invokables' => array(
                'Account\Controller\Account'  => 'Account\Controller\AccountController'
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
                    $fieldset = new Form\LoginFieldset($sm->get('translator'));
                    $form = new Form\LoginForm();
                    $hydrator = new DoctrineHydrator($entityManager, '\Account\Entity\Account');

                    $fieldset->setUseAsBaseFieldset(true)
                             ->setHydrator($hydrator)
                             ->setObject(new Entity\Account);

                    $form->add($fieldset)
                         ->setInputFilter(new InputFilter())
                         ->setHydrator($hydrator);

					return $form;
				},
				'account_register_form' => function($sm){
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
					$fieldset = new Form\RegisterFieldset($sm->get('translator'));
                    $form = new Form\RegisterForm();

                    $fieldset->setAccountRepository($entityManager->getRepository('\Account\Entity\Account'))
                             ->setUseAsBaseFieldset(true)
                             ->setHydrator(new DoctrineHydrator($entityManager, '\Account\Entity\Account'))
                             ->setObject(new Entity\Account);

					$form->add($fieldset)
                         ->setInputFilter(new InputFilter())
                         ->setHydrator(new DoctrineHydrator($entityManager, '\Account\Entity\Account'));
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
