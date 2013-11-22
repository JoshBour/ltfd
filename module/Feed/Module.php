<?php
namespace Feed;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\InputFilter\InputFilter;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getControllerConfig()
    {
        return array(
            'invokables' => array(
                'Feed\Controller\Feed' => 'Feed\Controller\FeedController'
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'feed_service' => function ($sm) {
                    return new Service\Feed();
                },
                'feed_queue_generator' => function($sm){
                    return new Model\FeedQueueGenerator();
                }
            ),
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
