<?php
namespace Account\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Account\Entity\Account;

class RegisterFieldset extends Fieldset implements InputFilterProviderInterface
{

    private $entityManager;
    private $translator;

    public function __construct($sm)
    {
        parent::__construct('account');

        $this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
        $this->translator = $sm->get('translator');
        $this->setHydrator(new DoctrineHydrator($this->entityManager, 'Account\Entity\Account'))
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
            'name' => 'email',
            'type' => 'email',
            'options' => array(
                'label' => $this->translator->translate('Email:'),
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate('Enter your email..')
            ),
        ));

    }

    public function getInputFilterSpecification()
    {
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
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->entityManager->getRepository('Account\Entity\Account'),
                            'fields' => 'username',
                            'messages' => array(
                                'objectFound' => $this->translator->translate("The username already exists, please select a different one.")
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => '/^[a-zA-Z0-9_]{4,16}$/',
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate("The name can only contain letters, numbers, underscores and no spaces between.")
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
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The password can't be empty.")
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
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
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\EmailAddress::INVALID_FORMAT => $this->translator->translate('Please input a valid email.'),
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->entityManager->getRepository('Account\Entity\Account'),
                            'fields' => 'email',
                            'messages' => array(
                                'objectFound' => $this->translator->translate("The email already exists, please select a different one.")
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }
}