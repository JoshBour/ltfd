<?php
namespace Admin\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Admin\Entity\Film;

class FilmFieldset extends Fieldset implements InputFilterProviderInterface{
	
	protected $entityManager;
	
	protected $translator;
	
	public function __construct($sm){
		
		parent::__construct('film');
		
		
		$this->entityManager = $sm->get('Doctrine\ORM\EntityManager');
		$this->translator = $sm->get('translator');
		$this->setHydrator(new DoctrineHydrator($this->entityManager,'Admin\Entity\Film'))
			->setObject(new Film());
		
		#$this->setAttribute('method','post');
		#$this->setAttribute('class','standardForm');

		$this->add(array(
			'name' => 'id',
			'type' => 'hidden'
		));		
				
		$this->add(array(
			'name' => 'name',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Name:')
			)
		));
		
		$this->add(array(
			'name' => 'nameEn',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Translated Name:')
			)
		));
		
		$this->add(array(
			'name' => 'description',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Description:')
			)
		));
		
		$this->add(array(
			'name' => 'descriptionEn',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Translated Description:')
			)
		));		
		
		$this->add(array(
			'name' => 'postTime',
			'type' => 'text',
			'attributes' => array(
				'class' => 'dateInput',
			),
			'options' => array(
				'label' => $this->translator->translate('Post Time:')
			)
		));		
		
        $this->add(
            array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'categories',
	            'attributes' => array(
	                'multiple' => 'multiple',
	            ),                
                'options' => array(
                    'object_manager' => $this->entityManager,
                    'target_class'   => 'Admin\Entity\Category',
                    'property'       => 'name',
                    'label'          => $this->translator->translate('Categories: <br /> (Press ctrl to select multiple)'),
                    'disable_inarray_validator' => true               
                ),
            )
        );		
		
		$this->add(array(
			'name' => 'video',
			'type' => 'text',
			'options' => array(
				'label' => $this->translator->translate('Vimeo Link:')
			)
		));	
		
		$this->add(array(
			'name' => 'snapshot',
			'type' => 'file',
			'options' => array(
				'label' => $this->translator->translate('Snapshot:'),
			)
		));		
		
		$this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'inSlide',
            'attributes' => array(
				'value' => "0"
			),
            'options' => array(
                'label' => $this->translator->translate('Show in slide:'),
            ),
        ));						
		
	}

	public function getInputFilterSpecification(){
		return array(
			'name' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The film's name can't be empty.")
							)
						)
					),
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 3,
							'messages' => array(
								\Zend\Validator\StringLength::TOO_SHORT => $this->translator->translate("The film's name must be more than 3 characters long.")
							)
						)
					),
					array(
						'name' => 'DoctrineModule\Validator\NoObjectExists',
						'options' => array(
							'object_repository' => $this->entityManager->getRepository('Admin\Entity\Film'),
							'fields' => 'name',
							'messages' => array(
								'objectFound' => $this->translator->translate("The film's name already exists, please select a different one.")
							)
						)
					)					
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)
			),
			'nameEn' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The translated film's name can't be empty.")
							)
						)
					),
					array(
						'name' => 'StringLength',
						'options' => array(
							'min' => 3,
							'messages' => array(
								\Zend\Validator\StringLength::TOO_SHORT => $this->translator->translate("The translated film's name must be more than 3 characters long.")
							)
						)
					),
					array(
						'name' => 'DoctrineModule\Validator\NoObjectExists',
						'options' => array(
							'object_repository' => $this->entityManager->getRepository('Admin\Entity\Film'),
							'fields' => 'nameEn',
							'messages' => array(
								'objectFound' => $this->translator->translate("The translated film's name already exists, please select a different one.")
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
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The description can't be empty.")
							)
						)
					),				
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)					
			),	
			'descriptionEn' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The translated description can't be empty.")
							)
						)
					),				
				),
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)					
			),
			'postTime' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'break_chain_on_failure' => true,
						'options' => array(
							'messages' => array(
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The post time can't be empty.")
							)
						)
					),				
				),				
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)					
			),
			'categories' => array(
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
							 	\Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate("The video can't be empty.")
							)
						)
					),				
				),				
			),		
			'snapshot' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				)					
			),						
		);	
	}

}
