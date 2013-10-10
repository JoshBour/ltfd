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

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    public function __construct(ServiceManager $sm)
    {
        parent::__construct('comment');

        $this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
        $this->translator = $sm->get('translator');

        $this->setHydrator(new DoctrineHydrator($this->entityManager, '\Feed\Entity\Comment'))
            ->setObject(new Comment());

        $this->add(array(
            'name' => 'id',
            'type' => 'hidden'
        ));

        $this->add(array(
            'name' => 'content',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => $this->translator->translate('Post a comment..')
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
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The comment can't be empty.")
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
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate("The comment length must be between 4-150 characters long.")
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
}