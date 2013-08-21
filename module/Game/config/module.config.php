<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'game' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/game/:name',
                    'defaults' => array(
                        'controller' => 'Game\Controller\Game',
                    )
                ),
//                'may_terminate' => true,
                'child_routes' => array(
                    'profile' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/profile',
                            'defaults' => array(
                                'controller' => 'Game\Controller\Game',
                                'action' => 'profile'
                            )
                        )
                    ),
                    'connect' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/connect/:type',
                            'defaults' => array(
                                'controller' => 'Game\Controller\Game',
                                'action' => 'connect'
                            ),
                            'constraints' => array(
                                'type' => 'follow|unfollow',
                            ),
                        ),
                    ),
                    'unfollow' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/unfollow',
                            'defaults' => array(
                                'controller' => 'Game\Controller\Game',
                                'action' => 'unfollow'
                            )
                        )
                    ),
                    'rate' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/rate/:rating',
                            'defaults' => array(
                                'controller' => 'Game\Controller\Game',
                                'action' => 'rate',
                            ),
                            'constraints' => array(
                                'rating' => 'up|down',
                            )
                        ),
                    ),
                    'category' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '[/:category]',
                            'defaults' => array(
                                'controller' => 'Game\Controller\Game',
                                'action' => 'feeds',
                                'category' => 'random'
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
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )
);
