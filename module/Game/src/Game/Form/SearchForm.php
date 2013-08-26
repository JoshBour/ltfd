<?php
namespace Game\Form;

use Zend\Form\Form;


class SearchForm extends Form{
    public function __construct($sm){
        parent::__construct('searchForm');

        $this->setAttributes(
            array(
                'method' => 'get',
                'id' => 'gameSearchForm'
            )
        );

        $this->add(array(
            'name' => 'gameSearch',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => $sm->get('translator')->translate("Type a game's name..")
            )
        ));
    }
}