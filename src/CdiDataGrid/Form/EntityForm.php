<?php

namespace CdiDataGrid\Form;

use Zend\Form\Form,
    Doctrine\Common\Persistence\ObjectManager,
    DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator,
    Zend\Form\Annotation\AnnotationBuilder;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import

class EntityForm extends Form {
 
    public function __construct() {
        parent::__construct('EntityForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', "form-horizontal");
        $this->setAttribute('role', "form");


        $this->addSubmitAndCsrf();
    }

    protected function addSubmitAndCsrf() {

        $this->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Submit'
            )
        ));
    }

    public function InputFilter() {

        $inputFilter = new InputFilter();
        //$factory = new InputFactory();



        return $inputFilter;
    }

}