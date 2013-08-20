<?php
namespace User\Form;

use Zend\Form\Form;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use User\Entity\User;

class Login extends Form{
	public function __construct($em){
		parent::__construct();
		
		#$this->setHydrator(new DoctrineEntity($em,'User\Entity\User'))->bind(new User());
		
		$this->setAttribute('method','post');
		$this->setAttribute('class','standardForm');
		
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
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'rememberme',
            'attributes' => array(
				'value' => "1"
			),
            'options' => array(
                'label' => 'Remember Me:',
                // 'value_options' => array(
                    // '0' => 'Checkbox', 
                    // '1' => 'Checkbox', 
                // ),
            ),
        ));	
		
		// $this->add(array(
			// 'type' => 'Zend\Form\Element\Csrf',
			// 'name' => 'security'
		// ));
		
		$this->add(array(
			'name' => 'submit',
				'type' => 'submit',
			'attributes' => array(
				'value' => 'Login'
			)
		));
		
	}
}
