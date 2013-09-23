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
                            'route' => 'user-feed-category',
                            'defaults' => array(
                                'action' => 'addToUserFeedCategory',
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
                    'delete' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'delete',
                            'defaults' => array(
                                'action' => 'delete',
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
                    'rate' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'rate/:rating/id/:id',
                            'defaults' => array(
                                'action' => 'rate',
                            ),
                            'constraints' => array(
                                'rating' => 'up|down'
                            )
                        ),
                    ),
                    'comment' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'comment/',
                            'defaults' => array(
                                'controller' => 'Feed/Controller/Comment'
                            )
                        ),
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => 'add',
                                    'defaults' => array(
                                        'action' => 'add',
                                    )
                                )
                            ),
                            'delete' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => 'delete',
                                    'defaults' => array(
                                        'action' => 'delete',
                                    )
                                )
                            ),
                            'report' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => 'report',
                                    'defaults' => array(
                                        'action' => 'report',
                                    )
                                )
                            ),
                            'edit' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => 'edit',
                                    'defaults' => array(
                                        'action' => 'edit',
                                    )
                                )
                            ),
                            'list' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => 'list',
                                    'defaults' => array(
                                        'action' => 'list',
                                    )
                                )
                            ),
                        )
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
