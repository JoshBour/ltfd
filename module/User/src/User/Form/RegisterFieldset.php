<?php
namespace User\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use User\Entity\User;

class RegisterFieldset extends Fieldset implements InputFilterProviderInterface{
	
	protected $entityManager;
	
	public function __construct($em){
		
		parent::__construct('user');
		
		
		$this->entityManager= $em;
		$this->setHydrator(new DoctrineEntity($em,'User\Entity\User'))
			->setObject(new User());
		
		#$this->setAttribute('method','post');
		#$this->setAttribute('class','standardForm');

		$this->add(array(
			'name' => 'id',
			'type' => 'hidden'
		));		
				
		$this->add(array(
			'name' => 'username',
			'type' => 'text',
			'options' => array(
				'label' => 'Username:'
			)
		));
		
		$this->add(array(
			'name' => 'password',
			'type' => 'password',
			'options' => array(
				'label' => 'Password:'
			)
		));
		
		$this->add(array(
			'name' => 'email',
			'type' => 'email',
			'options' => array(
				'label' => 'Email:'
			)
		));	
		
	}

	public function getInputFilterSpecification(){
		return array(
			'username' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => 'The username can\'t be empty.'
							)
						)
					),
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 4,
							'max' => 16,
							'messages' => array(
								\Zend\Validator\StringLength::TOO_LONG => 'The username must be between 4-16 characters long.',
								\Zend\Validator\StringLength::TOO_SHORT => 'The username must be between 4-16 characters long.'
							)
						)
					),
					array(
						'name' => 'DoctrineModule\Validator\NoObjectExists',
						'options' => array(
							'object_repository' => $this->entityManager->getRepository('User\Entity\User'),
							'fields' => 'username',
							'messages' => array(
								'objectFound' => 'The username already exists, please select a different one.'
							)
						)
					),
					array(
						'name' => 'DoctrineModule\Validator\NoObjectExists',
						'options' => array(
							'object_repository' => $this->entityManager->getRepository('User\Entity\User'),
							'fields' => 'email',
							'messages' => array(
								'objectFound' => 'The email already exists, please select a different one.'
							)
						)
					)					
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)
			),
			'password' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => 'The password can\'t be empty.'
							)
						)
					),
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 4,
							'max' => 16,
							'messages' => array(
								\Zend\Validator\StringLength::TOO_LONG => 'The password must be between 4-16 characters long.',
								\Zend\Validator\StringLength::TOO_SHORT => 'The password must be between 4-16 characters long.'
							)
						)
					)
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)						
			),
			'email' => array(
				'required' => false,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => 'The email can\'t be empty.'
							)
						)
					),
					array(
						'name' => 'EmailAddress',
						'options' => array(
							'messages' => array(
								\Zend\Validator\EmailAddress::INVALID => 'Please enter a valid email.'
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
