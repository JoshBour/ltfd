<?php
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
	        'zfcuser_entity' => array(
	            // customize path
	            'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
	            'paths' => array(__DIR__ . '/../src/Admin/Entity'),
	        ),
	        'orm_default' => array(
	            'drivers' => array(
	                'Admin\Entity' => 'zfcuser_entity',
	            ),
	        ),
	    ),
	),
    'router' => array(
        'routes' => array(
            'admin_main' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/admin[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'login',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
					'films' => array(
						'type' => 'segment',
						'options' => array(
							'route' => 'films[/[page/:page/][count/:count/][sort/:sort][/type/:type]]',
							'defaults' => array(
								'controller' => 'Admin\Controller\Film',
								'action' => 'list',
								'page' => 1,
								'count' => 10,
								'type' => 'asc',
								'sort' => 'id'
							),
							'constraints' => array(
								'page' => '[0-9]+',
								'count' => '[0-9]+'
							)
						),
						'may_terminate' => true,
						'child_routes' => array(
							'update' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => 'edit[/:id]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Film',
										'action' => 'edit'
									),
									'constraints' => array(
										'id' => '[0-9]+'
									)
								)
							),
							'delete' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => '[/]delete[/]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Film',
										'action' => 'delete'
									)
								)
							)							
						)
					),
					'accounts' => array(
						'type' => 'segment',
						'options' => array(
							'route' => 'accounts[/[page/:page/][count/:count/][sort/:sort][/type/:type]]',
							'defaults' => array(
								'controller' => 'Admin\Controller\Account',
								'action' => 'list',
								'page' => 1,
								'count' => 10,
								'type' => 'asc',
								'sort' => 'id'
							),
							'constraints' => array(
								'page' => '[0-9]+',
								'count' => '[0-9]+'
							)
						),
						'may_terminate' => true,
						'child_routes' => array(
							'update' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => 'edit[/:id]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Account',
										'action' => 'edit'
									),
									'constraints' => array(
										'id' => '[0-9]+'
									)
								)
							),
							'add' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => '[/]add[/]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Account',
										'action' => 'add'
									)
								)
							),
							'delete' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => '[/]delete[/]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Account',
										'action' => 'delete'
									)
								)
							)							
						)
					),
					'categories' => array(
						'type' => 'segment',
						'options' => array(
							'route' => 'categories[/[page/:page/][count/:count/][sort/:sort][/type/:type]]',
							'defaults' => array(
								'controller' => 'Admin\Controller\Category',
								'action' => 'list',
								'page' => 1,
								'count' => 10,
								'type' => 'asc',
								'sort' => 'id'
							),
							'constraints' => array(
								'page' => '[0-9]+',
								'count' => '[0-9]+'
							)
						),
						'may_terminate' => true,
						'child_routes' => array(
							'update' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => 'edit[/:id]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Category',
										'action' => 'edit'
									),
									'constraints' => array(
										'id' => '[0-9]+'
									)
								)
							),
							'add' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => '[/]add[/]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Category',
										'action' => 'add'
									)
								)
							),
							'delete' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => '[/]delete[/]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Category',
										'action' => 'delete'
									)
								)
							)							
						)				
					),
					'team' => array(
						'type' => 'segment',
						'options' => array(
							'route' => 'team[/[page/:page/][count/:count/][sort/:sort][/type/:type]]',
							'defaults' => array(
								'controller' => 'Admin\Controller\Team',
								'action' => 'list',
								'page' => 1,
								'count' => 10,
								'type' => 'asc',
								'sort' => 'id'
							),
							'constraints' => array(
								'page' => '[0-9]+',
								'count' => '[0-9]+'
							)
						),
						'may_terminate' => true,
						'child_routes' => array(
							'update' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => 'edit[/:id]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Team',
										'action' => 'edit'
									),
									'constraints' => array(
										'id' => '[0-9]+'
									)
								)
							),
							'add' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => '[/]add[/]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Team',
										'action' => 'add'
									)
								)
							),
							'delete' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => '[/]delete[/]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Team',
										'action' => 'delete'
									)
								)
							)							
						)					
					),
					'general' => array(
						'type' => 'segment',
						'options' => array(
							'route' => 'general[/[page/:page/][count/:count/][sort/:sort][/type/:type]]',
							'defaults' => array(
								'controller' => 'Admin\Controller\General',
								'action' => 'list',
								'page' => 1,
								'count' => 10,
								'type' => 'asc',
								'sort' => 'id'
							),
							'constraints' => array(
								'page' => '[0-9]+',
								'count' => '[0-9]+'
							)
						),
						'may_terminate' => true,
						'child_routes' => array(
							'update' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => 'edit[/:id]',
									'defaults' => array(
										'controller' => 'Admin\Controller\General',
										'action' => 'edit'
									),
									'constraints' => array(
										'id' => '[0-9]+'
									)
								)
							),							
						)					
					),	
					'sponsors' => array(
						'type' => 'segment',
						'options' => array(
							'route' => 'sponsors[/[page/:page/][count/:count/][sort/:sort][/type/:type]]',
							'defaults' => array(
								'controller' => 'Admin\Controller\Sponsor',
								'action' => 'list',
								'page' => 1,
								'count' => 10,
								'type' => 'asc',
								'sort' => 'id'
							),
							'constraints' => array(
								'page' => '[0-9]+',
								'count' => '[0-9]+'
							)
						),
						'may_terminate' => true,
						'child_routes' => array(
							'update' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => 'edit[/:id]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Sponsor',
										'action' => 'edit'
									),
									'constraints' => array(
										'id' => '[0-9]+'
									)
								)
							),
							'add' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => '[/]add[/]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Sponsor',
										'action' => 'add'
									)
								)
							),
							'delete' => array(
								'type' => 'Segment',
								'options' => array(
									'route' => '[/]delete[/]',
									'defaults' => array(
										'controller' => 'Admin\Controller\Sponsor',
										'action' => 'delete'
									)
								)
							)							
						)					
					),
				)
            ),                       
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'view_manager' => array(
    	'template_map' => array(
			'layout/admin' => __DIR__ . '/../view/layout/admin.phtml'
		),    
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
			'ViewJsonStrategy'
		)
    ),   
);
