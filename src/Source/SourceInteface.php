<?php

namespace CdiDataGrid\Source;

interface SourceInterface {

    public function query();

    public function setFilters();
    
    public function setOrder();
    
    //TODO
}

?>
