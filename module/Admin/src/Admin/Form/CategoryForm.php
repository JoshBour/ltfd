<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
use Admin\Entity\Category;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CategoryForm extends Form{
	
	public function __construct($em){
		parent::__construct('categoryForm');
		
		$this->setAttribute('method','post')
		     ->setAttribute('class','standardForm')
			 ->setHydrator(new DoctrineHydrator($em,'\Admin\Entity\Category'))
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
			'category' => array(
				'name',
				'nameEn',
				'position',
			)
		));
	}
	
	
}
