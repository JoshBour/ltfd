<?php
namespace Admin\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use Admin\Entity\Sponsor;

class SponsorFieldset extends Fieldset implements InputFilterProviderInterface{
	
	protected $entityManager;
	
	protected $translator;
	
	public function __construct($sm){
		
		parent::__construct('sponsor');
		
		
		$this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
		$this->translator = $sm->get('translator');
		$this->setHydrator(new DoctrineEntity($this->entityManager,'Admin\Entity\Sponsor'))
			->setObject(new Sponsor());
		
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
			'name' => 'url',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Url:')
			)
		));			
		
		$this->add(array(
			'name' => 'image',
			'type' => 'file',
			'options' => array(
				'label' => $this->translator->translate('Logo:'),
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
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The name can't be empty.")
							)
						)
					),
					array(
						'name' => 'DoctrineModule\Validator\NoObjectExists',
						'options' => array(
							'object_repository' => $this->entityManager->getRepository('Admin\Entity\Sponsor'),
							'fields' => 'name',
							'messages' => array(
								'objectFound' => $this->translator->translate("The sponsor's name already exists, please select a different one.")
							)
						)
					)					
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)
			),	
			'url' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The url can't be empty.")
							)
						)
					),					
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)
			),			
			'image' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The image can't be empty.")
							)
						)
					),
				)				
			),	
		);	
	}

}
