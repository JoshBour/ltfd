<?php
namespace Admin;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
	public function onBootstrap(MvcEvent $e)
	{
		#\Locale::setDefault('de_DE');
		
		$eventManager        = $e->getApplication()->getEventManager();
		


		#$translator = $e->getApplication()->getServiceManager()->get('translator');
		#\Zend\Validator\AbstractValidator::setDefaultTranslator($translator);

		$sharedManager = $eventManager->getSharedManager();
		$sharedManager->attach(__NAMESPACE__,'dispatch',function($e){
			$controller = $e->getTarget();
			$controller->layout('layout/admin');
		});
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
				'Zend\Loader\StandardAutoloader' => array(
						'namespaces' => array(
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
						),
				),
		);
	}
	
	public function getControllerConfig(){
		return array(
	        'invokables' => array(
	            'Admin\Controller\Account' => 'Admin\Controller\AccountController',
	            'Admin\Controller\Film' => 'Admin\Controller\FilmController',
	            'Admin\Controller\Category' => 'Admin\Controller\CategoryController',
	            'Admin\Controller\Team' => 'Admin\Controller\TeamController',
	            'Admin\Controller\General' => 'Admin\Controller\GeneralController',
	            'Admin\Controller\Sponsor' => 'Admin\Controller\SponsorController',
	        ),
		);
	}
	
	public function getServiceConfig(){
		return array(
			'factories' => array(
				'category_form' => function($sm){
					$em = $sm->get('Doctrine\ORM\EntityManager');
					$fieldset = new \Admin\Form\CategoryFieldset($sm);
					$fieldset->setUseAsBaseFieldset(true);
					$form = new \Admin\Form\CategoryForm($em);
					$form->add($fieldset);
					return $form;
				},
				'team_form' => function($sm){
					$em = $sm->get('Doctrine\ORM\EntityManager');
					$fieldset = new \Admin\Form\TeamFieldset($sm);
					$fieldset->setUseAsBaseFieldset(true);
					$form = new \Admin\Form\TeamForm($em);
					$form->add($fieldset);
					return $form;
				},
				'general_form' => function($sm){
					$em = $sm->get('Doctrine\ORM\EntityManager');
					$fieldset = new \Admin\Form\GeneralFieldset($sm);
					$fieldset->setUseAsBaseFieldset(true);
					$form = new \Admin\Form\GeneralForm($em);
					$form->add($fieldset);
					return $form;
				},
				'sponsor_form' => function($sm){
					$em = $sm->get('Doctrine\ORM\EntityManager');
					$fieldset = new \Admin\Form\SponsorFieldset($sm);
					$fieldset->setUseAsBaseFieldset(true);
					$form = new \Admin\Form\SponsorForm($em);
					$form->add($fieldset);
					return $form;
				},
				'film_form' => function($sm){
					$em = $sm->get('Doctrine\ORM\EntityManager');
					$fieldset = new \Admin\Form\FilmFieldset($sm);
					$fieldset->setUseAsBaseFieldset(true);
					$form = new \Admin\Form\FilmForm($em);
					$form->add($fieldset);
					return $form;
				}							
			)
		);
	}
}
