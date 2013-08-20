<?php
namespace Admin\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use Admin\Entity\Category;

class CategoryFieldset extends Fieldset implements InputFilterProviderInterface{
	
	protected $entityManager;
	
	protected $translator;
	
	public function __construct($sm){
		
		parent::__construct('category');
		
		
		$this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
		$this->translator = $sm->get('translator');
		$this->setHydrator(new DoctrineEntity($this->entityManager,'Admin\Entity\Category'))
			->setObject(new Category());
		
		#$this->setAttribute('method','post');
		#$this->setAttribute('class','standardForm');

		$this->add(array(
			'name' => 'id',
			'type' => 'hidden'
		));		
				
		$this->add(array(
			'name' => 'name',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Name:')
			)
		));
		
		$this->add(array(
			'name' => 'nameEn',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Translated Name:')
			)
		));
		
		$this->add(array(
			'name' => 'position',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Position:')
			)
		));		
		
	}

	public function getInputFilterSpecification(){
		return array(
			'name' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The category's name can't be empty.")
							)
						)
					),
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 3,
							'max' => 25,
							'messages' => array(
								\Zend\Validator\StringLength::TOO_LONG => $this->translator->translate("The category's name must be between 3-25 characters long."),
								\Zend\Validator\StringLength::TOO_SHORT => $this->translator->translate("The category's name must be between 3-25 characters long.")
							)
						)
					),
					array(
						'name' => 'DoctrineModule\Validator\NoObjectExists',
						'options' => array(
							'object_repository' => $this->entityManager->getRepository('Admin\Entity\Category'),
							'fields' => 'name',
							'messages' => array(
								'objectFound' => $this->translator->translate("The category's name already exists, please select a different one.")
							)
						)
					)					
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)
			),
			'nameEn' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The translated category's name can't be empty.")
							)
						)
					),
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 3,
							'max' => 25,
							'messages' => array(
								\Zend\Validator\StringLength::TOO_LONG => $this->translator->translate("The translated category's name must be between 3-25 characters long."),
								\Zend\Validator\StringLength::TOO_SHORT => $this->translator->translate("The translated category's name must be between 3-25 characters long.")
							)
						)
					),
					array(
						'name' => 'DoctrineModule\Validator\NoObjectExists',
						'options' => array(
							'object_repository' => $this->entityManager->getRepository('Admin\Entity\Category'),
							'fields' => 'nameEn',
							'messages' => array(
								'objectFound' => $this->translator->translate("The translated category's name already exists, please select a different one.")
							)
						)
					)					
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)					
			),
			'position' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The position can't be empty.")
							)
						)
					),				
					array(
						'name' => '\Zend\Validator\Digits',
						'options' => array(
							'messages' => array(
								\Zend\Validator\Digits::NOT_DIGITS => $this->translator->translate('The position must be a valid number.')
							)
						)
					)
				)
			)
		);	
	}

}
