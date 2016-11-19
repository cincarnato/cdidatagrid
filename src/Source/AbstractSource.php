<?php

namespace CdiDataGrid\Source;

use CdiDataGrid\EventManager\EventProvider;

abstract class AbstractSource extends EventProvider implements SourceInterface {

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var mixed
     */
    protected $data;

       /**
     * Order
     * 
     * @var \CdiDataGrid\Sort
     */
    protected $sort;

    /**
     * @var CdiDataGrid\Filter\Filters
     */
    protected $filters;

    /**
     * The data result.
     *
     * @var \Zend\Paginator\Adapter\AdapterInterface
     */
    protected $paginatorAdapter;
    
    /**
     * Description
     * 
     * @var \Zend\Log\Logger
     */
    protected $log;

    function getData() {
        return $this->data;
    }

    function setData($data) {
        $this->data = $data;
    }


    public function getPaginatorAdapter() {
        return $this->paginatorAdapter;
    }

    public function setPaginatorAdapter($paginatorAdapter) {
        $this->paginatorAdapter = $paginatorAdapter;
    }

    function getColumns() {
        return $this->columns;
    }

    function setColumns($columns) {
        $this->columns = $columns;
    }


    function getSort() {
        return $this->sort;
    }

    function setSort(\CdiDataGrid\Sort $sort) {
        $this->sort = $sort;
    }

        
    public function addFilter(\CdiDataGrid\Filter\Filter $filter) {
        $this->getFilters()->addFilter($filter);
    }

    /**
     * Get Filters
     *
     * @return \CdiDataGrid\Filters
     */
    function getFilters() {
        if (isset($this->filters)) {
            $this->setFilters(new \CdiDataGrid\Filter\Filters());
        }
        return $this->filters;
    }

    function setFilters(\CdiDataGrid\Filters $filters) {
        $this->filters = $filters;
    }
    
    function getLog() {
        return $this->log;
    }

    function setLog(\Zend\Log\Logger $log) {
        $this->log = $log;
    }



}

?>
