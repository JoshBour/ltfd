<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent as MvcEvent;
use Zend\Session\Container;

class Module {
	public function onBootstrap(MvcEvent $e) {
		$app = $e -> getApplication();
		$eventManager = $app -> getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener -> attach($eventManager);

//        $eventManager->attach('dispatch',function($e){
//            $routeMatch = $e->getRouteMatch();
//            $viewModel = $e->getViewModel();
//            $viewModel->setVariable('action',$routeMatch->getParam('action'));
//        },-100);
	}

    public function getServiceConfig(){
        return array(
            'factories' => array(
                'cache' => function () {
                    return \Zend\Cache\StorageFactory::factory(array(
                        'adapter' => array(
                            'name' => 'filesystem',
                            'options' => array(
                                'cache_dir' => __DIR__ . '/../../data/cache',
                                'ttl' => 100
                            ),
                        ),
                        'plugins' => array(
                            array(
                                'name' => 'serializer',
                                'options' => array(

                                )
                            )
                        )
                    ));
                },
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
