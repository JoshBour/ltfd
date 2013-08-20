<?php
namespace Account\Validator;

use Zend\Validator\AbstractValidator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UsernameExists extends AbstractValidator implements ServiceLocatorAwareInterface{
	
	const ERROR = 'derp';
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em = null;
	
	protected $services;
	
	protected $messageTemplates = array(
		self::ERROR => "'%value%' already exists."
	);
	
	public function __construct(){
		$this->setEntityManager($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
	}
	
	public function isValid($value){
		$this->setValue($value);
		$repository = $this->em->getRepository('Account\Entity\Account');
		$validator = new \DoctrineModule\Validator\ObjectExists(array(
			'object_repository' => $repository,
			'fields' => array('username')
		));
		if(!$validator->isValid($value)){
			$this->error(self::ERROR);
			return false;
		}
		return true;
	}
	
	public function setEntityManager($em){
		$this->em = $em;
	}
	

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
         $this->services = $serviceLocator;
    }

    public function getServiceLocator()
    {
         return $this->services;
    }	
}
