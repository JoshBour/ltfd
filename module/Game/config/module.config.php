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
					'route' => '/game/:name[/]',
					'defaults' => array(
						'controller' => 'Game\Controller\Game',
						'action' => 'view',
					),
				),
 				'may_terminate' => true,
 				'child_routes' => array(
 					'feeds' => array(
 						'type' => 'literal',
 						'options' => array(
 							'route' => 'feeds',
 							'defaults' => array(
 								'controller' => 'Game\Controller\Game',
 								'action' => 'feeds'		
 							)		
 						)
 					),
 					'follow' => array(
 						'type' => 'literal',
 						'options' => array(
 							'route' => 'follow',
 							'defaults' => array(
 								'controller' => 'Game\Controller\Game',
 								'action' => 'follow'		
 							)		
 						)
 					),
 					'unfollow' => array(
 						'type' => 'literal',
 						'options' => array(
 							'route' => 'unfollow',
 							'defaults' => array(
 								'controller' => 'Game\Controller\Game',
 								'action' => 'unfollow'		
 							)		
 						)
 					),
 					'suggest' => array(
 						'type' => 'literal',
 						'options' => array(
 							'route' => 'suggest',
 							'defaults' => array(
 								'controller' => 'Game\Controller\Game',
 								'action' => 'suggest'		
 							)		
 						)
 					)
 				),
			),	
			'games_list' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/games[/]',
					'defaults' => array(
						'controller' => 'Game\Controller\Game',
						'action' => 'list'
					)
				)
			)		
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ), 
);
