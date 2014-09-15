<?php

namespace CdiDataGrid\DataGrid\Source;


abstract class AbstractSource {
    
    protected $paginatorAdapter;
    
    public function getPaginatorAdapter() {
        return $this->paginatorAdapter;
    }

    public function setPaginatorAdapter($paginatorAdapter) {
        $this->paginatorAdapter = $paginatorAdapter;
    }


    
    
}


?>
