<?php
namespace Account\Form;

use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Account\Entity\Account;
use Zend\InputFilter\InputFilter;

class RegisterForm extends Form
{
    public function __construct($em)
    {
        parent::__construct('registerForm');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'standardForm'
        ));

        $this->setHydrator(new DoctrineHydrator($em, '\Account\Entity\Account'));
        $this->setInputFilter(new InputFilter());

        $this->add(array(
            'name' => 'security',
            'type' => 'Zend\Form\Element\Csrf'
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit'
        ));

        $this->setValidationGroup(array(
            'security',
            'account' => array(
                'username',
                'password',
                'email'
            )
        ));
    }
}