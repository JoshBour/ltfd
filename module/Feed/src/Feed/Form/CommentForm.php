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
    public function __construct()
    {
        parent::__construct('commentForm');

        $this->setAttributes(array(
            'method' => 'post',
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit'
        ));

        $this->setValidationGroup(array(
            'comment' => array(
                'id',
                'content'
            )
        ));
    }
}