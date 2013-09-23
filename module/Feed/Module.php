<?php
namespace Feed;

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
                'Feed\Controller\Feed' => 'Feed\Controller\FeedController',
                'Feed\Controller\Comment' => 'Feed\Controller\CommentController'
            )
        );
    }

    public function getServiceConfig(){
        return array(
            'factories' => array(
                'comment_form' => function($sm){
                    $fieldset = new Form\CommentFieldset($sm);
                    $fieldset->setUseAsBaseFieldset(true);
                    $form = new Form\CommentForm($sm->get('Doctrine\ORM\EntityManager'));
                    $form->add($fieldset);
                    return $form;
                },
                'feed_form' => function($sm){
                    $fieldset = new Form\FeedFieldset($sm);
                    $fieldset->setUseAsBaseFieldset(true);
                    $form = new Form\FeedForm($sm->get('Doctrine\ORM\EntityManager'));
                    $form->add($fieldset);
                    return $form;
                }
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
