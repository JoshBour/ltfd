<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
use Admin\Entity\Film;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class FilmForm extends Form{
	
	public function __construct($em){
		parent::__construct('filmForm');
		
		$this->setAttribute('method','post')
		     ->setAttribute('class','standardForm')
			 ->setHydrator(new DoctrineHydrator($em,'\Admin\Entity\Film'))
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
			'film' => array(
				'name',
				'nameEn',
				'description',
				'descriptionEn',
				'postTime',
				'categories',
				'video',
				'snapshot',
				'inSlide',
			)
		));
	}
	
	
}
