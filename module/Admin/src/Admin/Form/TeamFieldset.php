<?php
namespace Admin\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use Admin\Entity\Member;

class TeamFieldset extends Fieldset implements InputFilterProviderInterface{
	
	protected $entityManager;
	
	protected $translator;
	
	public function __construct($sm){
		
		parent::__construct('member');
		
		
		$this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
		$this->translator = $sm->get('translator');
		$this->setHydrator(new DoctrineEntity($this->entityManager,'Admin\Entity\Member'))
			->setObject(new Member());
		
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
			'name' => 'position',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Position:')
			)
		));		
		
		$this->add(array(
			'name' => 'positionEn',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Translated Position:')
			)
		));		
		
		$this->add(array(
			'name' => 'avatar',
			'type' => 'file',
			'options' => array(
				'label' => $this->translator->translate('Avatar:'),
			)
		));				
		
		$this->add(array(
			'name' => 'email',
			'type' => 'email',
			'options' => array(
				'label' => $this->translator->translate('Email')
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
							'object_repository' => $this->entityManager->getRepository('Admin\Entity\Member'),
							'fields' => 'name',
							'messages' => array(
								'objectFound' => $this->translator->translate("The member's name already exists, please select a different one.")
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
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)
			),
			'positionEn' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The translated position can't be empty.")
							)
						)
					),					
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)
			),	
			'avatar' => array(
				'required' => true,
		        'validators' => array(
		            array(
		            	'name' => 'filesize',
						'break_chain_on_failure' => true,
		            	'options' => array(
		               	 	'max' => 5000000,
		               	 	'messages' => array(
								\Zend\Validator\File\Size::TOO_BIG => $this->translator->translate('The image must be less than 5mb')
							)
		               	 )
		            ),
		           // array(
		           		// // 'name' => '\Zend\Validator\File\MimeType',
		           		// // 'options' => array(
		           			// // 'disableMagicFile' => true,
							// // 'mimeType' => array(IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG),
							// // 'messages' => array(
								// // \Zend\Validator\File\MimeType::FALSE_TYPE => 'The image must be of types: jpeg, png, gif.'
								// // )
							// // )
						// ),
		        ),				
				// 'filters' => array(
					// array(
						// 'name' => 'filerenameupload',
						// 'options' => array(
							// 'target' => PUBLIC_PATH . '/images/users',
							// 'overwrite' => true,
							// 'use_upload_name' => true,
							// 'randomize' => true,
						// )
					// )
				// )
			),		
			'email' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The email can't be empty.")
							)
						)
					),
					array(
						'name' => 'EmailAddress',
						'options' => array(
							'messages' => array(
								\Zend\Validator\EmailAddress::INVALID => $this->translator->translate('Please enter a valid email.')
							)
						)
					
					)				
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)							
			)
		);	
	}

}
