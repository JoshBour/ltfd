<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
use User\Entity\User;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class RegisterForm extends Form{
	
	public function __construct($em){
		parent::__construct('registerForm');
		
		$this->setAttribute('method','post')
		     ->setAttribute('class','standardForm')
			 ->setHydrator(new DoctrineHydrator($em,'\User\Entity\User'))
			 ->setInputFilter(new InputFilter());

		// $this->add(array(
			// 'type' => new RegisterFieldset($em),
			// 'options' => array(
				// 'user_as_base_fieldset' => true
			// )
		// ));
		
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
			'feed' => array(
				'username',
				'password',
				'email'
			)
		));
	}
	
	
}
