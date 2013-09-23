<?php
/**
 * User: Josh
 * Date: 18/9/2013
 * Time: 10:26 μμ
 */

namespace Feed\Form;

use Zend\Form\Fieldset;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\ServiceManager;
use Zend\InputFilter\InputFilterProviderInterface;
use Feed\Entity\Feed;

class FeedFieldset extends Fieldset implements InputFilterProviderInterface{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var Zend\I18n\Translator
     */
    private $translator;

    public function __construct(ServiceManager $sm){
        parent::__construct('feed');

        $this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
        $this->translator = $sm->get('translator');

        $this->setHydrator(new DoctrineHydrator($this->entityManager, 'Feed\Entity\Feed'))
            ->setObject(new Feed());

        $this->add(array(
            'name' => 'title',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate('Title:')
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate('Enter a custom feed title.')
            )
        ));

        $this->add(array(
            'name' => 'video',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate('Youtube Video URL:')
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate("Enter the video's URL.")
            )
        ));

        $this->add(
            array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'game',
                'options' => array(
                    'object_manager' => $this->entityManager,
                    'target_class'   => 'Game\Entity\Game',
                    'property'       => 'name',
                    'label'          => $this->translator->translate('Game:'),
                    'disable_inarray_validator' => true,
                    'empty_option' => $this->translator->translate('First select a game.')
                ),
            )
        );

        $this->add(
            array(
                'type' => 'select',
                'name' => 'category',
                'options' => array(
                    'label' => $this->translator->translate('Category:'),
                    'empty_option' => $this->translator->translate('First select a game.')
                )
            )
        );
    }

    public function getInputFilterSpecification(){
        return array(
            'title' => array(
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 50,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate("The title must be between 4-50 characters long.")
                            )
                        )
                    )
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'video' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The video URL can't be empty.")
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/",
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate('The youtube video URL has invalid format.')
                            )
                        )
                    )
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'category' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }
}