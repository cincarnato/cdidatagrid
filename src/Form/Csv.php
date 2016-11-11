<?php

namespace CdiDataGrid\Form;

use Zend\Form\Form,
    Doctrine\Common\Persistence\ObjectManager,
    DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator,
    Zend\Form\Annotation\AnnotationBuilder;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import

class Csv extends Form {

    public function __construct() {
        parent::__construct('csvExport');
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', "form-horizontal");
        $this->setAttribute('role', "form");
        $this->setAttribute('action', "javascript:submitCsv()");

        $this->add(array(
            'name' => 'csvExport',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => 'yes'
            )
        ));


        /*
         * Input Text
         */
        $this->add(array(
            'name' => 'nameCsv',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => false,
                'class' => "form-control",
                'placeholder' => ""
            ),
            'options' => array(
                'label' => 'Nombre del archivo',
            )
        ));


        /*
         * Input Text
         */
        $this->add(array(
            'name' => 'clave',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'required' => false,
                'class' => "form-control",
                'placeholder' => ""
            ),
            'options' => array(
                'label' => 'clave',
            )
        ));



        /*
         * Input Select - Array (Example estados)
         */
        $options = array("coma" => "Coma",
            "puntoycoma" => "Punto y coma",
            "tabulador" => "Tabulador");
        $this->add(array(
            'name' => 'separatorCsv',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'required' => false,
                'class' => "form-control"
            ),
            'options' => array(
                'label' => 'Separador',
                'value_options' => $options
            ),
        ));



        $this->addSubmitAndCsrf();
    }

    protected function addSubmitAndCsrf() {

        $this->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Descargar'
            )
        ));
    }

    public function InputFilter() {

        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add($factory->createInput(array(
                    'name' => 'clave',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'Regex',
                            'options' => array(
                                'pattern' => '/^shura77$/',
                                'messages' => array(
                                    'Invalid password'
                                ),
                            ),
                        ),
                    ),
        )));



        return $inputFilter;
    }

}
