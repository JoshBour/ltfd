<?php
namespace Account\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Account\Entity\Account;

class LoginFieldset extends Fieldset implements InputFilterProviderInterface{

    private $entityManager;
    private $translator;

    public function __construct($sm){
        parent::__construct('account');

        $this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
        $this->translator = $sm->get('translator');
        $this->setHydrator(new DoctrineHydrator($this->entityManager,'Account\Entity\Account'))
             ->setObject(new Account());

        $this->add(array(
            'name' => 'username',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate('Username:')
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate('Enter your username..')
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'options' => array(
                'label' => $this->translator->translate('Password:')
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate('Enter your password..')
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'remember',
            'attributes' => array(
                'value' => "1"
            ),
            'options' => array(
                'label' => $this->translator->translate('Remember Me:'),
            ),
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
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The username can't be empty.")
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 15,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate("The username is invalid.")
                            )
                        )
                    ),
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
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The password can't be empty.")
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 15,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate("The password is invalid.")
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            )
        );
    }
}