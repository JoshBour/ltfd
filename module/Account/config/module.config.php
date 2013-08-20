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
	            'paths' => array(__DIR__ . '/../src/Account/Entity'),
	        ),
	        'orm_default' => array(
	            'drivers' => array(
	                'Account\Entity' => 'entity',
	            ),
	        ),
	    ),
		'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Account\Entity\Account',
                'identity_property' => 'username',
                'credential_property' => 'password',
                'credential_callable' => 'Account\Entity\Account::hashPassword'
            ),
        ),	    
	),
    'router' => array(
        'routes' => array(
         	'account' => array(
         		'type' => 'Segment',
         		'options' => array(
         			'route' => '/account[/]',
         			'defaults' => array(
         				'controller' => 'Account\Controller\Account'
         			),
         			'may_terminate' => false,
         			'child_routes' => array(
	         			'login' => array(
	         					'type' => 'Segment',
	         					'options' => array(
	         							'route' => 'login[/]',
	         							'defaults' => array(
	         									'controller' => 'Account\Controller\Account',
	         									'action'     => 'login',
	         							)
	         					)
	         			),
         				'logout' => array(
         						'type' => 'Segment',
         						'options' => array(
         								'route' => 'logout[/]',
         								'defaults' => array(
         										'controller' => 'Account\Controller\Account',
         										'action'     => 'logout',
         								)
         						)
         				), 
         				'register' => array(
         						'type' => 'Segment',
         						'options' => array(
         								'route' => 'register[/]',
         								'defaults' => array(
         										'controller' => 'Account\Controller\Account',
         										'action'     => 'register',
         								)
         						)
         				),         				
         				'delete' => array(
         						'type' => 'Segment',
         						'options' => array(
         								'route' => 'delete[/]',
         								'defaults' => array(
         										'controller' => 'Account\Controller\Account',
         										'action'     => 'delete',
         								)
         						)
         				),
         				'verify' => array(
         						'type' => 'Segment',
         						'options' => array(
         								'route' => 'verify/:code',
         								'defaults' => array(
         										'controller' => 'Account\Controller\Account',
         										'action'     => 'verify',
         								)
         						)
         				), 
         				'report' => array(
         						'type' => 'Segment',
         						'options' => array(
         								'route' => 'report[/]',
         								'defaults' => array(
         										'controller' => 'Account\Controller\Account',
         										'action'     => 'report',
         								)
         						)
         				),
         			)
         		)
         	),                         
        ), // routes array end
    ), 
    'view_manager' => array(
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
    ),
);
