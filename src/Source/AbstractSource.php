<?php

namespace CdiDataGrid\Source;

use CdiDataGrid\EventManager\EventProvider;
use \CdiDataGrid\Source\SourceInterface;

abstract class AbstractSource extends EventProvider implements SourceInterface {

    protected $paginatorAdapter;

    public function getPaginatorAdapter() {
        return $this->paginatorAdapter;
    }

    public function setPaginatorAdapter($paginatorAdapter) {
        $this->paginatorAdapter = $paginatorAdapter;
    }

    public function query();

    public function setFilters();

    public function setOrder();
}

?>
