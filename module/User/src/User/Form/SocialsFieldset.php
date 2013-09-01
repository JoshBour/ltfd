<?php
namespace User\Form;

use Zend\Form\Fieldset;
use Account\Entity\Account;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\InputFilter\InputFilterProviderInterface;


class SocialsFieldset extends Fieldset implements InputFilterProviderInterface
{

    private $entityManager;

    private $translator;

    public function __construct($sm)
    {
        parent::__construct('social');

        $this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
        $this->translator = $sm->get('translator');
        $this->setHydrator(new DoctrineHydrator($this->entityManager, 'Account\Entity\Account'))
            ->setObject(new Account());

        $this->add(array(
            'name' => 'id',
            'type' => 'hidden'
        ));

        $this->add(array(
            'name' => 'facebook',
            'type' => 'text',
            'attributes' => array(
              'placeholder' => $this->translator->translate('e.g www.facebook.com/leetfeed')
            ),
            'options' => array(
                'label' => $this->translator->translate('Facebook:')
            )
        ));

        $this->add(array(
            'name' => 'twitter',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => $this->translator->translate('e.g www.twitter.com/leetfeed')
            ),
            'options' => array(
                'label' => $this->translator->translate('Twitter:')
            )
        ));

        $this->add(array(
            'name' => 'youtube',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => $this->translator->translate('e.g www.youtube.com/leetfeed')
            ),
            'options' => array(
                'label' => $this->translator->translate('Youtube:')
            )
        ));

        $this->add(array(
            'name' => 'website',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => $this->translator->translate('e.g www.leetfeed.com')
            ),
            'options' => array(
                'label' => $this->translator->translate('Website:')
            )
        ));

    }

    public function getInputFilterSpecification()
    {
        return array(
            'facebook' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The twitter URL can't be empty.")
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'pattern' => '#^(http[s]?:\/\/)?((www|[a-zA-Z]{2}-[a-zA-Z]{2})\.)?facebook\.com/[a-zA-Z0-9-]+$#',
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate("The facebook URL is of invalid format.")
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'twitter' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The twitter URL can't be empty.")
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'pattern' => '#^(http[s]?:\/\/)?twitter\.com/(\#!/)?[a-zA-Z0-9]{1,15}[/]?$#',
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate("The twitter URL is of invalid format.")
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'youtube' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The youtube URL can't be empty.")
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'pattern' => '#^(http[s]?:\/\/)?(?:www\.)?youtube.com\/.{6,20}$#',
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate("The youtube URL is of invalid format.")
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'website' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The website URL can't be empty.")
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'pattern' => "#/\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))#",
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate("The website URL is of invalid format.")
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