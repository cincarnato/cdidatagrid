<?php

namespace CdiDataGrid\DataGrid\Source;
use CdiCommons\EventManager\EventProvider;

abstract class AbstractSource extends EventProvider {
    
    protected $paginatorAdapter;
    
    public function getPaginatorAdapter() {
        return $this->paginatorAdapter;
    }

    public function setPaginatorAdapter($paginatorAdapter) {
        $this->paginatorAdapter = $paginatorAdapter;
    }


    
    
}


?>
