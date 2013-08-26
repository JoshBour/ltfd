<?php
namespace Application;
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
 			'language' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/language/:lang',
					'defaults' => array(
						'controller' => 'Application\Controller\Language',
						'action' => 'change',
						'lang' => 'en'
					),
				),
			),	        
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'home',
                    ),
                ),
            ),
            'about' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/about[/]',
					'defaults' => array(
						'controller' => 'Application\Controller\Index',
						'action' => 'about',
					),
				),
			),
            'faq' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/faq[/]',
					'defaults' => array(
						'controller' => 'Application\Controller\Index',
						'action' => 'faq',
					),
				),
			),	
            'tos' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/tos[/]',
					'defaults' => array(
						'controller' => 'Application\Controller\Index',
						'action' => 'tos',
					),
				),
			),
            'team' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/team[/]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'team',
                    ),
                ),
            ),
            'contact' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/contact[/]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'contact',
                    ),
                ),
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
        'locale' => 'nb_NO',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Language' => 'Application\Controller\LanguageController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'layout/mobile'           => __DIR__ . '/../view/layout/mobile.phtml',
            'layout/homepage'           => __DIR__ . '/../view/layout/homepage.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ), 
);
