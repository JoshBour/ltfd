<?php
namespace User\Form;

use Zend\Form\Fieldset;
use Account\Entity\Account;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\InputFilter\InputFilterProviderInterface;


class DetailsFieldset extends Fieldset implements InputFilterProviderInterface
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
            'name' => 'id',
            'type' => 'hidden'
        ));

        $this->add(array(
            'name' => 'avatar',
            'type' => 'file',
            'options' => array(
                'label' => $this->translator->translate('Avatar:')
            )
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'options' => array(
                'label' => $this->translator->translate('Change Password:')
            )
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'options' => array(
                'label' => $this->translator->translate('Email:')
            )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'avatar' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => '\Zend\Validator\File\IsImage',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'max' => 5000000,
                            'messages' => array(
                                \Zend\Validator\File\IsImage::FALSE_TYPE => $this->translator->translate('The avatar must be a readable image.')
                            )
                        )
                    ),
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
                    array(
                        'name' => '\Zend\Validator\File\MimeType',
                        'options' => array(
                            'mimeType' => array('image/jpeg', 'image/png'),
                            'enableHeaderCheck' => true,
                            'messages' => array(
                                \Zend\Validator\File\MimeType::FALSE_TYPE => $this->translator->translate('The image must be of types jpeg or png.')
                            )
                        )
                    ),
                ),
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
//            'repassword' => array(
//                'required' => true,
//                'validators' => array(
//                    array(
//                        'name' => 'NotEmpty',
//                        'break_chain_on_failure' => true,
//                        'options' => array(
//                            'messages' => array(
//                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The retyped password can't be empty.")
//                            )
//                        )
//                    ),
//                    array(
//                        'name' => 'StringLength',
//                        'break_chain_on_failure' => true,
//                        'options' => array(
//                            'min' => 4,
//                            'max' => 15,
//                            'messages' => array(
//                                \Zend\Validator\StringLength::INVALID => $this->translator->translate("The retyped password is invalid.")
//                            )
//                        )
//                    ),
//                ),
//                'filters' => array(
//                    array('name' => 'StringTrim'),
//                    array('name' => 'StripTags')
//                )
//            ),
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