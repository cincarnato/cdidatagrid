<?php

namespace CdiDataGrid\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

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
