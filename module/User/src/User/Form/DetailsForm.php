<?php
namespace User\Form;

use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\InputFilter\InputFilter;

class DetailsForm extends Form
{

    public function __construct($em)
    {
        parent::__construct('detailsForm');

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
            'type' => 'submit',
        ));

        $this->setValidationGroup(array(
            'security',
            'repassword',
            'avatar',
            'account' => array(
                'password',
                'email'
            )
        ));
    }
}