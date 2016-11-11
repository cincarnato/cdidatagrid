<?php

namespace CdiDataGrid\Column;

/**
 * Description of Column
 *
 * @author cincarnato
 */
class ExtraColumn extends AbstractColumn{
 
  
    
    protected $side;
    protected $originalValue;
    
    protected $filterActive = true;
    
    protected $filter;
    
    function __construct($name,$side) {
        $this->name = $name;
    $this->visualName = $name;
    $this->type = "extra";
        $this->setSide($side);
    }

    
 
    
    public function __toString() {
        return $this->visualName;
    }
    
    public function getSide() {
        return $this->side;
    }

    public function setSide($side) {
        if ($side == "left" || $side == "right") {
            $this->side = $side;
        } else {
            throw new Exception("The side must be 'left' or 'right'");
        }
    }



    public function getFilterActive() {
        return $this->filterActive;
    }

    public function setFilterActive($filterActive) {
        $this->filterActive = $filterActive;
    }

    public function getFilter() {
        return $this->filter;
    }

    public function setFilter($filter) {
        $this->filter = $filter;
    }

    public function getOriginalValue() {
        return $this->originalValue;
    }

    public function setOriginalValue($originalValue) {
        $this->originalValue = $originalValue;
    }








    
}

?>
