<?php
namespace CdiDataGrid\Decorator;

/**
 * Description of Rename
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class Rename extends AbstractDecorator {

    
    /**
     * New Visual Name of the column
     * 
     * @var string
     */
    protected $newName;


    function getNewName() {
        return $this->newName;
    }

    function setNewName($newName) {
        $this->newName = $newName;
    }


    
}
