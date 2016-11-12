<?php

namespace CdiDataGrid\Source;

interface SourceInterface {

    /**
     * Set the data source
     * - array
     * - Doctrine2: Doctrine\ORM\QueryBuilder
     * - ...
     *
     * @param mixed $data
     */
    public function __construct($data);

    /**
     * @return mixed
     */
    public function getData();

    /**
     * $param array $Columns
     */
    public function setColumns($Columns);

    /**
     * @return array $Columns
     */
    public function pullColumns();

    /**
     * Execute the query and set the paginator
     * - with sort statements
     * - with filters statements.
     */
    public function execute();

    /**
     * Set sort conditions.
     *
     * @param Column\AbstractColumn $column
     * @param string                $sortDirection
     */
    public function addSortCondition(\CdiDataGrid\Column\ColumnInterface $column, $sortDirection = 'ASC');

    /**
     * @param Filter $filters
     */
    public function addFilter(\CdiDataGrid\Filter\Filter $filter);
}

?>