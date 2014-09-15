<?php

namespace CdiDataGrid\DataGrid\Source;

interface SourceInterface {

    public function query();

    public function setFilters();
    
    public function setOrder();
}

?>
