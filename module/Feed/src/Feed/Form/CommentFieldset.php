<?php
/**
 * User: Josh
 * Date: 17/9/2013
 * Time: 5:24 μμ
 */

namespace Feed\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\ServiceManager;
use Feed\Entity\Comment;

class CommentFieldset extends Fieldset implements InputFilterProviderInterface
{

    const PLACEHOLDER_POST_COMMENT = "Enter a comment..";

    const ERROR_COMMENT_EMPTY = "The comment can't be empty.";
    const ERROR_COMMENT_INVALID = "The comment length must be between 4-150 characters long.";

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    public function __construct()
    {
        parent::__construct('comment');

        $this->add(array(
            'name' => 'id',
            'type' => 'hidden'
        ));

        $this->add(array(
            'name' => 'content',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => $this->translator->translate(self::PLACEHOLDER_POST_COMMENT)
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'content' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_COMMENT_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'min' => 4,
                            'max' => 150,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_COMMENT_INVALID)
                            )
                        )
                    )
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            )
        );
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