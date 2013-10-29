<?php
/**
 * User: Josh
 * Date: 18/9/2013
 * Time: 10:22 μμ
 */

namespace Feed\Form;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\InputFilter\InputFilter;

class FeedForm extends Form{
    public function __construct(){
        parent::__construct('feedForm');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'standardForm'
        ));

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
            'feed' => array(
                'title',
                'description',
                'video',
                'game',
            )
        ));
    }
}