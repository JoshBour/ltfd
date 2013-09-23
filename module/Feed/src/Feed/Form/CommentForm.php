<?php
/**
 * User: Josh
 * Date: 17/9/2013
 * Time: 5:24 μμ
 */

namespace Feed\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CommentForm extends Form
{
    public function __construct($em)
    {
        parent::__construct('commentForm');

        $this->setAttributes(array(
            'method' => 'post',
        ));

        $this->setHydrator(new DoctrineHydrator($em, '\Feed\Entity\Comment'))
            ->setInputFilter(new InputFilter());

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
            'comment' => array(
                'id',
                'content'
            )
        ));
    }
}