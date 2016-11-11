<?php
namespace CdiDataGrid\Decorator;

/**
 * Description of AbstractDecorator
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
abstract class AbstractDecorator implements DecoratorInterface {

    
   /**
     * Real Column's name (key)
     * 
     * @var string
     */
    protected $columnName;
    
    function getColumnName() {
        return $this->columnName;
    }

    function setColumnName($columnName) {
        $this->columnName = $columnName;
    }


}
