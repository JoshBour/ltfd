<?php
namespace Feed;
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'doctrine' => array(
        'driver' => array(
            'entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => 'entity',
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'feed' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/feed/',
                    'defaults' => array(
                        'controller' => 'Feed\Controller\Feed',
                    )
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'watched' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'add-to-watched',
                            'defaults' => array(
                                'action' => 'addToWatched',
                            ),
                        ),
                    ),
                    'favorite' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'set-favorite',
                            'defaults' => array(
                                'action' => 'setFavorite',
                            ),
                        ),
                    ),
                    'new' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'new',
                            'defaults' => array(
                                'action' => 'new',
                            ),
                        ),
                    ),
                    'remove' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'remove',
                            'defaults' => array(
                                'action' => 'remove',
                            ),
                        ),
                    ),
                    'rate' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'rate',
                            'defaults' => array(
                                'action' => 'rate',
                            ),
                        ),
                    ),
                    'report' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'report',
                            'defaults' => array(
                                'action' => 'report',
                            ),
                        ),
                    ),
                    'view' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':id',
                            'defaults' => array(
                                'action' => 'view',
                            ),
                            'constraints' => array(
                                'id' => '[0-9]+'
                            )
                        ),
                    ),
                )
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    ),
);
