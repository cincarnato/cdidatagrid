<?php
namespace CdiDataGrid\Decorator;

/**
 * Description of Hidden
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class Hidden extends AbstractDecorator {
  
    /**
     * Define if column show or hide
     * 
     * @var boolean
     */
    protected $hidden = true;
    
    function getHidden() {
        return $this->hidden;
    }

    function setHidden($hidden) {
        $this->hidden = $hidden;
    }


}
