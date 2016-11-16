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
     * @var array
     */
    protected $sortConditions = [];

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

    /**
     * Set sort conditions.
     *
     * @param \CdiDataGrid\Column\ColumnInterface $column
     * @param string $sortDirection
     */
    public function addSortCondition(\CdiDataGrid\Column\ColumnInterface $column, $sortDirection = 'ASC') {

        if ($sortDirection != 'ASC' && $sortDirection != 'DESC') {
            throw new \CdiDataGrid\Exception\SortConditionException;
        }

        $this->sortConditions[] = [
            'column' => $column,
            'sortDirection' => $sortDirection,
        ];
    }

    function getSortConditions() {
        return $this->sortConditions;
    }

    function setSortConditions($sortConditions) {
        $this->sortConditions = $sortConditions;
    }

    public function addFilter(\CdiDataGrid\Filter\Filter $filter) {
        $this->getFilters()->addFilter($filter);
    }

    /**
     * Get Filters
     *
     * @return \CdiDataGrid\Filter\Filters
     */
    function getFilters() {
        if (isset($this->filters)) {
            $this->setFilters(new \CdiDataGrid\Filter\Filters());
        }
        return $this->filters;
    }

    function setFilters(\CdiDataGrid\Filter\Filters $filters) {
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
