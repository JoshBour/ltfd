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

class FeedFieldset extends Fieldset implements InputFilterProviderInterface
{

    const PLACEHOLDER_FEED_TITLE = "Enter a custom feed title.";
    const PLACEHOLDER_FEED_DESCRIPTION = "Enter a custom feed description.";
    const PLACEHOLDER_FEED_VIDEO_URL = "Enter the videos URL.";
    const PLACEHOLDER_FEED_GAME = "Select a game.";

    const LABEL_FEED_TITLE = "Title:";
    const LABEL_FEED_DESCRIPTION = "Description:";
    const LABEL_FEED_VIDEO_URL = "Youtube Video URL:";
    const LABEL_FEED_GAME = "Game:";

    const ERROR_TITLE_INVALID = "The title must be between 4-50 characters long.";
    const ERROR_DESCRIPTION_INVALID = "The title must be between 4-50 characters long.";
    const ERROR_EMPTY_GAME = "You must select a game for the video.";
    const ERROR_EMPTY_VIDEO_URL = "You must select a game for the video.";
    const ERROR_INVALID_VIDEO_URL = "The youtube video URL has invalid format.";

    const VIDEO_URL_REGEX_PATTERN = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/";


    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function __construct()
    {
        parent::__construct('feed');

        $this->add(array(
            'name' => 'title',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_FEED_TITLE)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_FEED_TITLE)
            )
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'textarea',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_FEED_DESCRIPTION)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_FEED_DESCRIPTION)
            )
        ));

        $this->add(array(
            'name' => 'video',
            'type' => 'text',
            'options' => array(
                'label' => $this->translator->translate(self::LABEL_FEED_VIDEO_URL)
            ),
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_FEED_VIDEO_URL)
            )
        ));

        $this->add(
            array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'game',
                'options' => array(
                    'object_manager' => $this->entityManager,
                    'target_class' => 'Game\Entity\Game',
                    'property' => 'name',
                    'label' => $this->translator->translate(self::LABEL_FEED_GAME),
                    'disable_inarray_validator' => true,
                    'empty_option' => $this->translator->translate(self::PLACEHOLDER_FEED_GAME)
                ),
            )
        );
    }

    public function getInputFilterSpecification()
    {
        return array(
            'title' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 50,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_TITLE_INVALID)
                            )
                        )
                    )
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'description' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 200,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_DESCRIPTION_INVALID)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'game' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_EMPTY_GAME)
                            )
                        )
                    ),
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
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_EMPTY_VIDEO_URL)
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => self::VIDEO_URL_REGEX_PATTERN,
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate(self::ERROR_INVALID_VIDEO_URL)
                            )
                        )
                    )
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }

    /**
     * Set the entity manager.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return LoginFieldset
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get the entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Set the zend translator.
     *
     * @param \Zend\I18n\Translator\Translator $translator
     * @return LoginFieldset
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Get the zend translator.
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }


}