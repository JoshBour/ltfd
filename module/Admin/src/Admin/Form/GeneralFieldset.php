<?php
namespace Admin\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use Admin\Entity\General;

class GeneralFieldset extends Fieldset implements InputFilterProviderInterface{
	
	protected $entityManager;
	
	protected $translator;
	
	public function __construct($sm){
		
		parent::__construct('general');
		
		
		$this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
		$this->translator = $sm->get('translator');
		$this->setHydrator(new DoctrineEntity($this->entityManager,'Admin\Entity\General'))
			->setObject(new General());
		
		#$this->setAttribute('method','post');
		#$this->setAttribute('class','standardForm');

		$this->add(array(
			'name' => 'id',
			'type' => 'hidden'
		));		
				
		$this->add(array(
			'name' => 'content',
			'type' => 'textarea',
			'options' => array(
				'label' => $this->translator->translate('Content:')
			)
		));
		
		$this->add(array(
			'name' => 'contentEn',
			'type' => 'textarea',
			'options' => array(
				'label' => $this->translator->translate('Translated Content:')
			)
		));		
		
		
	}

	public function getInputFilterSpecification(){
		return array(
			'content' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The content can't be empty.")
							)
						)
					),				
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array(
						'name' => 'StripTags',
						'options' => array(
							'allowTags' => array('a','br','strong','b','i')
							)
						)
				)
			),
			'contentEn' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The translated content can't be empty.")
							)
						)
					),				
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array(
						'name' => 'StripTags',
						'options' => array(
							'allowTags' => array('a','br','strong','b','i','h1','h2','h3','p')
							)
						)
				)
			),
		);	
	}

}
