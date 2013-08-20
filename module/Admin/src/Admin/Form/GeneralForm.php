<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
use Admin\Entity\General;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class GeneralForm extends Form{
	
	public function __construct($em){
		parent::__construct('generalForm');
		
		$this->setAttribute('method','post')
		     ->setAttribute('class','standardForm')
			 ->setHydrator(new DoctrineHydrator($em,'\Admin\Entity\General'))
			 ->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'security',
			'type' => 'Zend\Form\Element\Csrf'
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'submit',
		));
		
		$this->setValidationGroup(array(
			'security',
			'general' => array(
				'content',
				'contentEn',
			)
		));
	}
	
	
}
