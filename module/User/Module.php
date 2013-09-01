<?php
namespace User;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
	public function onBootstrap(MvcEvent $e)
	{
		$eventManager        = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);

        $eventManager->attach('dispatch',array($this,'isLoggedIn'), 10);
	}
//
//    public function init(ModuleManager $moduleManager)
//    {
//        // Remember to keep the init() method as lightweight as possible
//        $events = $moduleManager->getEventManager();
//
//    }


    public function isLoggedIn(MvcEvent $e){
        $identity = $e->getApplication()->getServiceManager()->get('auth_service');
        $controller = explode('\\',$e->getRouteMatch()->getParam('controller'));
        if(!$identity->hasIdentity()){
            if($controller[0] == "User"){
                $url = $e->getRouter()->assemble(array(), array('name' => 'login'));
                $response=$e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();
                return $response;
            }
        }else{
            $e->getViewModel()->setVariable('user',$identity->getIdentity());
        }
    }

    public function getServiceConfig(){
        return array(
            'factories' => array(
                'user_socials_form' => function($sm){
                    $em = $sm->get('Doctrine\ORM\EntityManager');
                    $fieldset = new \User\Form\SocialsFieldset($sm);
                    $fieldset->setUseAsBaseFieldset(true);
                    $form = new \User\Form\SocialsForm($em);
                    $form->add($fieldset);
                    return $form;
                },
                'user_details_form' => function($sm){
                    $em = $sm->get('Doctrine\ORM\EntityManager');
                    $fieldset = new \User\Form\DetailsFieldset($sm);
                    $fieldset->setUseAsBaseFieldset(true);
                    $form = new \User\Form\DetailsForm($em);
                    $form->add($fieldset);
                    return $form;
                },
            )
        );
    }

	public function getControllerConfig(){
		return array(
				'invokables' => array(
						'User\Controller\User' => 'User\Controller\UserController'
				)
		);
	}	
	
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
				'Zend\Loader\ClassMapAutoloader' => array(
						__DIR__ . '/autoload_classmap.php',
						),
				'Zend\Loader\StandardAutoloader' => array(
						'namespaces' => array(
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
						),
				),
		);
	}
}
