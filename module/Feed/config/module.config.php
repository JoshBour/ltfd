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
	        'entity' => array(
	            'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
	            'paths' => array(__DIR__ . '/../src/User/Entity'),
	        ),
	        'orm_default' => array(
	            'drivers' => array(
	                'User\Entity' => 'entity',
	            ),
	        ),
	    ),
		'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'User\Entity\User',
                'identity_property' => 'username',
                'credential_property' => 'password',
                'credential_callable' => 'User\Entity\User::hashPassword'
            ),
        ),	    
	),
    'router' => array(
        'routes' => array(
            // 'profile' => array(
                // 'type' => 'Segment',
                // 'options' => array(
                    // 'route'    => '/admin[/]',
                    // 'defaults' => array(
                        // 'controller' => 'User\Controller\User',
                        // 'action'     => 'login',
                    // ),
                // ),
            // ),
            'account-login' => array(
            	'type' => 'Segment',
            	'options' => array(
            		'route' => '/admin/login[/]',
            		'defaults' => array(
            			'controller' => 'User\Controller\User',
            			'action'     => 'login',
            		)
            	)
            ),
            'account-register' => array(
            	'type' => 'Segment',
            	'options' => array(
            		'route' => '/accounts[/]',
            		'defaults' => array(
            			'controller' => 'User\Controller\User',
            			'action'     => 'list',
            		)
            	)
            ),
            'account-logout' => array(
            	'type' => 'Segment',
            	'options' => array(
            		'route' => '/[admin/]logout[/]',
            		'defaults' => array(
            			'controller' => 'User\Controller\User',
            			'action'     => 'logout',
            		)
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
			'layout/login' => __DIR__ . '/../view/layout/login.phtml'
		),     
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
	'zfcuser' => array(
	    'user_entity_class'       => 'User\Entity\User',
	    'enable_default_entities' => false,
	),    
);
