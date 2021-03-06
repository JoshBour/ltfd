<?php
namespace Game;
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
            'game' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/game/:name[/]',
                    'defaults' => array(
                        'controller' => 'Game\Controller\Game',
                    ),
                    'constraints' => array(
                        'name' => '[a-zA-Z0-9_!]{1,50}'
                    )
                ),
//                'may_terminate' => true,
                'child_routes' => array(
                    'feeds' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '[:category[/:page[/:index]]]',
                            'defaults' => array(
                                'controller' => 'Game\Controller\Game',
                                'action' => 'feeds',
                                'category' => 'feeds',
                                'page' => 1,
                                'index' => 1
                            )
                        )
                    ),
                    'rate' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'rate/:rating',
                            'defaults' => array(
                                'controller' => 'Game\Controller\Game',
                                'action' => 'rate',
                            ),
                            'constraints' => array(
                                'rating' => 'up|down',
                            )
                        ),
                    ),
                    'profile' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'profile',
                            'defaults' => array(
                                'controller' => 'Game\Controller\Game',
                                'action' => 'profile'
                            )
                        )
                    ),
                ),
            ),
            'games' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/games[/]',
                    'defaults' => array(
                        'controller' => 'Game\Controller\Game',
                        'action' => 'list'
                    )
                )
            ),
            'suggest' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/game/suggest',
                    'defaults' => array(
                        'controller' => 'Game\Controller\Game',
                        'action' => 'suggest'
                    )
                )
            ),
            'search' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/game/search/name/:name',
                    'defaults' => array(
                        'controller' => 'Game\Controller\Game',
                        'action' => 'search'
                    )
                )
            ),
            'game_connect' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/game/connect/:type/id/:id',
                    'defaults' => array(
                        'controller' => 'Game\Controller\Game',
                        'action' => 'connect'
                    ),
                    'constraints' => array(
                        'type' => 'follow|unfollow',
                    ),
                ),
            ),
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    )
);
