<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class LanguageController extends AbstractActionController
{
	
	public function changeAction(){
		$session = new Container('base');
		$response = $this->getResponse();
		$lang = $this->params('lang',null);
		if($lang == 'en'){
			$session->locale = 'en_US';
		}else if($lang == 'no'){
			$session->locale = 'nb_NO';
		}else{
			$session->locale = null;
		}
		#$url = $this->getRequest()->getHeader('Referer')->getUri();
		#$this->redirect()->toUrl($url);
		return $response;
	}
	
	
}
