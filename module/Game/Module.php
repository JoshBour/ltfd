<?php
namespace Game;

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

    public function getServiceConfig(){
        return array(
            'factories' => array(
                'game_search_form' => function($sm){
                    return new Form\SearchForm($sm);
                }
            )
        );
    }

	public function getControllerConfig(){
		return array(
				'invokables' => array(
						'Game\Controller\Game' => 'Game\Controller\GameController'
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
