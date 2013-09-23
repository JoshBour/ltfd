<?php
namespace User;
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
        	'profile' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/:user[/]',
        			'defaults' => array(
        				'controller' => 'User\Controller\User',
        				'action' => 'profile'
        			)
        		)
        	),
        	'user' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/user',
        			'defaults' => array(
        				'controller' => 'User\Controller\User',
        			)
        		),
        		'may_terminate' => false,
        		'child_routes' => array(
        			'details' => array(
        				'type' => 'literal',
        				'options' => array(
	        				'route' => '/details',
	        				'defaults' => array(
	        					'action' => 'details'
	        				)
        				)
        			),
        			'socials' => array(
        				'type' => 'literal',
        				'options' => array(
	        				'route' => '/socials',
	        				'defaults' => array(
	        					'action' => 'socials'
	        				)
        				)
        			),
        			'games' => array(
        				'type' => 'literal',
        				'options' => array(
	        				'route' => '/games',
	        				'defaults' => array(
	        					'action' => 'games'
	        				)
        				)
        			),
        			'following' => array(
        				'type' => 'literal',
        				'options' => array(
	        				'route' => '/following',
	        				'defaults' => array(
	        					'action' => 'following'
	        				)
        				)
        			),
        			'follow' => array(
        				'type' => 'literal',
        				'options' => array(
	        				'route' => '/follow',
	        				'defaults' => array(
	        					'action' => 'follow'
	        				)
        				)
        			),
        			'unfollow' => array(
        				'type' => 'literal',
        				'options' => array(
	        				'route' => '/unfollow',
	        				'defaults' => array(
	        					'action' => 'unfollow'
	        				)
        				)
        			),
        			'feeds' => array(
        				'type' => 'segment',
        				'options' => array(
	        				'route' => '/feeds[/:category]',
	        				'defaults' => array(
	        					'action' => 'feeds'
	        				),
                            'constraints' => array(
                                'category' => 'posted|favorites|history|liked'
                            )
        				)
        			),
        		)
        	),                      
        ),
    ),
    'view_manager' => array(   
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),  
);
