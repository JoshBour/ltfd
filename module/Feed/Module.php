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
                'Feed\Controller\Comment' => 'Feed\Controller\CommentController',
                'Feed\Controller\Feed' => 'Feed\Controller\FeedController'
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'comment_form' => function ($sm) {
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                    $fieldset = new Form\CommentFieldset();
                    $form = new Form\CommentForm();
                    $hydrator = new DoctrineHydrator($entityManager, 'Entity\Comment');

                    $fieldset->setUseAsBaseFieldset(true)
                        ->setTranslator($sm->get('translator'))
                        ->setHydrator($hydrator)
                        ->setObject(new Entity\Comment());

                    $form->add($fieldset)
                        ->setInputFilter(new InputFilter())
                        ->setHydrator($hydrator);

                    return $form;
                },
                'feed_form' => function ($sm) {
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                    $fieldset = new Form\FeedFieldset();
                    $form = new Form\FeedForm();
                    $hydrator = new DoctrineHydrator($entityManager, 'Entity\Feed');

                    $fieldset->setUseAsBaseFieldset(true)
                        ->setTranslator($sm->get('translator'))
                        ->setEntityManager($entityManager)
                        ->setHydrator($hydrator)
                        ->setObject(new Entity\Feed());

                    $form->add($fieldset)
                        ->setInputFilter(new InputFilter())
                        ->setHydrator($hydrator);

                    return $form;
                },
                'feed_service' => function ($sm) {
                    return new Service\Feed();
                },
                'comment_service' => function ($sm) {
                    return new Service\Comment();
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
