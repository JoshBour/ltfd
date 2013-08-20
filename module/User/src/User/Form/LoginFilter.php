<?php
namespace User\Form;

use Zend\InputFilter\InputFilter;

class LoginFilter extends InputFilter{
	
	public function __construct(){
		$this->add(array(
			'name' => 'username',
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
				)
			)
		));
		
		$this->add(array(
			'name' => 'password',
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
			)
		));		
		
	}
	
	
}
