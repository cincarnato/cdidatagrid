<?php

namespace CdiDataGrid\Factory;

/**
 * Description of FormFilter
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class FormFilterFactory {

    protected $gridId;

    function __construct($gridId) {
        $this->gridId = $gridId;
    }

    public function create($form, $page, $data) {
        $name = 'cdiGridFormFilters_' . $this->gridId;
        $form->setName($name);
        $form->setAttribute('method', 'get');

        foreach ($form as $key => $element) {

            //TODO - ADD F_


            if ($element instanceof \DoctrineModule\Form\Element\ObjectSelect) {
                $element->setOption("display_empty_item", true);
                $element->setOption("empty_item_label", "---");
                $element->setEmptyOption("---");
            }

            if (preg_match("/hidden/i", $element->getAttribute("type")) && $element->getName() == 'id') {
                $newElement = new \Zend\Form\Element\Text('id');
                $form->remove($element->getName());
                $form->add($newElement);
            }


            if (preg_match("/textarea/i", $element->getAttribute("type"))) {
                $name = $element->getName();
                $newElement = new \Zend\Form\Element\Text($name);
                $form->remove($element->getName());
                $form->add($newElement);
            }

            if (preg_match("/number/i", $element->getAttribute("type"))) {
                $name = $element->getName();
                $newElement = new \Zend\Form\Element\Text($name);
                $form->remove($element->getName());
                $form->add($newElement);
            }

            if (preg_match("/checkbox/i", $element->getAttribute("type"))) {
                $name = $element->getName();

                $newElement = new \Zend\Form\Element\Select($name);
                $newElement->setOptions(array(
                    'value_options' => array(0 => "false", 1 => "true"),
                    'empty_option' => ''
                ));
                $newElement->setLabel($name);

                $form->remove($element->getName());
                $form->add($newElement);
            }


            if (preg_match("/file/i", $element->getAttribute("type"))) {
                $form->remove($element->getName());
            }
        }

//        foreach ($this->forceFilters as $key => $element) {
//            if ($form->has($key)) {
//                $form->remove($key);
//            }
//            $form->add($element);
//        }

        $form->add(array(
            'name' => 'page',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $page
            )
        ));

        $form->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Filter'
            )
        ));

        $form->setData($data);
        return $form;
    }

}
