<?php

namespace CdiDataGrid\DataGrid\Column;

/**
 * Description of Column
 *
 * @author cincarnato
 */
class Column extends AbstractColumn{
 
    protected $filterActive = true;
    
    protected $filter;
    
    function __construct($name) {
        $this->name = $name;
        $this->visualName = $name;
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
 
}

?>
