<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;

	/**
	 * @var Zend\I18n\Translator\Translator
	 */
	private $translator;
	
	private $sponsors;
	
	private $general;
		
    public function homeAction()
    {
    	$bodyClass = 'homepage';
		$this->layout('layout/homepage');
 		$em = $this->getEntityManager();
		$locale = $this->getTranslator()->getLocale();
		$general = $this->getGeneral();
		$filmRepo = $em->getRepository('\Admin\Entity\Film');
		$slideFilms = $filmRepo->findBy(array('inSlide' => 1));
		$latestFilms = $filmRepo->findBy(array(),array('postTime' => 'asc'),5);
		$sponsors = $this->getSponsors();   
		
        return new ViewModel(array(
        				'bodyClass' => $bodyClass,
						'general' => $general,
						'locale' => $locale,
						'slideFilms' => $slideFilms, 
						'sponsors' => $sponsors,
						'latestFilms' => $latestFilms
						));
    }
	
    public function portfolioAction()
    {
 		$em = $this->getEntityManager();
		$locale = $this->getTranslator()->getLocale();
		$general = $this->getGeneral();
		
		$activeCategory = $this->params('category','all');
		$filmRepo = $em->getRepository('Admin\Entity\Film');
		$catRepo = $em->getRepository('Admin\Entity\Category');
		if($activeCategory == 'all'){
			$films = $filmRepo->findAll();
		}else{
			$category = $catRepo->findByNameOrTranslatedName($activeCategory);
			if(!empty($category)){
				$films = $category[0]->getFilms();
			}else{
				$films = $filmRepo->findAll();
			}
		}
		$sponsors = $this->getSponsors();   	
		
		return new ViewModel(array('films' => $films, 
								'general' => $general, 
								'activeCategory' => $activeCategory,
								'categories' => $catRepo->findBy(array(),array('position' => 'asc')),
								'locale' => $locale, 
								'sponsors' => $sponsors));
    }
	
	public function teamAction(){
		$em = $this->getEntityManager();
		$locale = $this->getTranslator()->getLocale();
		$general = $this->getGeneral();
		$repository = $em->getRepository('Admin\Entity\Member');
		$sponsors = $this->getSponsors();
		
		$members = $repository->findAll();
		
		
		return new ViewModel(array('members' => $members, 'general' => $general, 'locale' => $locale, 'sponsors' => $sponsors));
	}
	
	public function contactAction(){
		return new ViewModel();
	}
	
	public function getEntityManager() {
		if (!$this -> entityManager) {
			$this -> setEntityManager($this -> getServiceLocator() -> get('Doctrine\ORM\EntityManager'));
		}
		return $this -> entityManager;
	}

	public function setEntityManager($em) {
		$this -> entityManager = $em;
	}

	public function getTranslator() {
		if (!$this -> translator) {
			$this -> setTranslator($this -> getServiceLocator() -> get('translator'));
		}
		return $this -> translator;
	}

	public function setTranslator($translator) {
		$this -> translator = $translator;
	}
	
	public function getSponsors(){
		if(!$this->sponsors){
			$this->setSponsors($this->getEntityManager()->getRepository('Admin\Entity\Sponsor')->findAll());
		}
		return $this->sponsors;
	}
	
	public function setSponsors($sponsors){
		$this->sponsors = $sponsors;
	}
	
	public function getGeneral(){
		if(!$this->general){
			$this->setGeneral($this->getEntityManager()->getRepository('Admin\Entity\General')->getGeneralArray($this->getTranslator()->getLocale()));
		}
		return $this->general;
	}
	
	public function setGeneral($general){
		$this->general = $general;
	}	
	
	
}
