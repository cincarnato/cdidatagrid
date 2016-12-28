<?php

namespace CdiDataGrid;

use \Zend\Paginator\Paginator;
use CdiDataGrid\Column\ExtraColumn;
use CdiDataGrid\Column\CrudColumn;

/**
 * Main Class for GRID
 *
 * @author cincarnato
 */
class Grid {

    /**
     * Identificador del Grid
     * 
     * @var string
     */
    protected $id = "CdiGrid";

    /**
     * Data source of grid
     *
     * @var \CdiDataGrid\Source\SourceInterface
     */
    protected $source;

    /**
     * HTTP REQUEST FROM APPLICATION-MVCEVENT
     *
     * @var \Zend\Mvc\MvcEvent
     */
    protected $mvcevent;

    /**
     * HTTP REQUEST FROM APPLICATION-MVCEVENT
     *
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * RouteMatch FROM APPLICATION-MVCEVENT
     *
     * @var \Zend\Router\RouteMatch
     */
    protected $routeMatch;

    /**
     * Number of Page (paginator)
     *
     * @var integer
     */
    protected $page;

    /**
     * Grid's Paginator
     *
     * @var \Zend\Paginator\Paginator
     */
    protected $paginator;

    /**
     * Basic and unprocessed records of the grid
     *
     * @var Array
     */
    protected $data;

    /**
     * 
     *
     * @var Array
     */
    protected $row;

    /**
     * A factory for columns
     *
     * @var \CdiDataGrid\Factory\ColumnFactory
     */
    protected $columnFactory;

    /**
     * A factory for formFilter
     *
     * @var \CdiDataGrid\Factory\FormFilterFactory
     */
    protected $formFilterFactory;

    /**
     * A columns collection
     * 
     * @var array
     */
    protected $columns = array();

    /**
     * A extra columns collection
     * 
     * @var array
     */
    protected $extraColumns = array();

    /**
     * A crud columns collection
     * 
     * @var array
     */
    protected $crudColumn = array();

    /**
     * Description
     * 
     * @var type
     */
    protected $filters;

    /**
     * Define instance to render
     * 
     * @var type
     */
    protected $instanceToRender = "grid";

    /**
     * CRUD
     * 
     * @var \CdiDataGrid\Crud
     */
    protected $crud;


    //CONFIG REFACTOR

    /**
     * Grid Options
     *
     * @var Array
     */
    protected $options;

    /**
     * Template a renderzar.
     * 
     * @var string
     */
    protected $template = "default";

    /**
     * Defined if the Grid has been prepared
     * 
     * @var type
     */
    protected $ready = false;

    /**
     * Order
     * 
     * @var \CdiDataGrid\Sort
     */
    protected $sort;
    //TOREVIEW

    protected $formFilters;
    protected $editForm = null;
    protected $tableClass;
    protected $recordDetail;
    protected $forceFilters = array();

    /**
     * Construct
     * 
     * @param \Zend\Mvc\MvcEvent $mvcevent
     */
    public function __construct(\Zend\Mvc\MvcEvent $mvcevent, \CdiDataGrid\Options\GridOptionsInterface $options) {

        $this->setMvcevent($mvcevent);

        $this->setOptions($options);
    }

    function getMvcevent() {
        return $this->mvcevent;
    }

    function setMvcevent(\Zend\Mvc\MvcEvent $mvcevent) {
        $this->mvcevent = $mvcevent;
    }

    function getSort() {
        return $this->sort;
    }

    function setSort(\CdiDataGrid\Sort $sort) {
        $this->sort = $sort;
    }

    //-->CONFIG

    public function getOptions() {
        return $this->options;
    }

    public function setOptions(\CdiDataGrid\Options\GridOptionsInterface $options) {
        $this->options = $options;
    }

    function getColumnsConfig() {
        return $this->getOptions()->getColumnsConfig();
    }

    function setColumnsConfig(Array $columnsConfig) {
        $this->getOptions()->setColumnsConfig($columnsConfig);
    }
    
     function mergeColumnsConfig(Array $columnsConfig) {
        $this->getOptions()->mergeColumnsConfig($columnsConfig);
    }

    function getCrudConfig() {
        return $this->getOptions()->getCrudConfig();
    }

    function setCrudConfig(Array $crudConfig) {
        $this->getOptions()->setCrudConfig($crudConfig);
    }

    public function getRecordPerPage() {
        return $this->recordPerPage;
    }

    public function setRecordsPerPage($recordsPerPage) {
        $this->getOptions()->setRecordsPerPage($recordsPerPage);
    }

// DISABLED - SourceConfig Conflict - set Custom Config only on Factory    
//    public function setCustomOptions($customOptionsKey) {
//        $this->getOptions()->mergeCustomOptionsByKey($customOptionsKey);
//    }
    //<--CONFIG
    //
    //
    //-->>SOURCE

    /**
     * Get Source of grid
     *
     * @return \CdiDataGrid\Source\SourceInterface
     */
    function getSource() {
        return $this->source;
    }

    /**
     * Set Source of grid
     *
     * @param \CdiDataGrid\Source\SourceInterface $source Source of grid
     * @return \CdiDataGrid\Source\SourceInterface
     */
    function setSource(\CdiDataGrid\Source\SourceInterface $source) {
        $this->source = $source;

        return $this->source;
    }

    //<<--SOURCE
    //
    //
    //-->COLUMNS

    protected function buildColumns() {
        $sourceColumnsName = $this->getSource()->pullColumns();

        foreach ($sourceColumnsName as $name) {
            $this->createColumn($name);
        }
    }

    protected function createColumn($name) {
        if (key_exists($name, $this->getColumnsConfig())) {
            $columnConfig = $this->getColumnsConfig()[$name];
        } else {
            $columnConfig = array();
        }
        $this->columns[$name] = $this->getColumnFactory()->create($name, $columnConfig);
    }

    function getColumnFactory() {
        if (!isset($this->columnFactory)) {
            $this->setColumnFactory(new Factory\ColumnFactory);
        }
        return $this->columnFactory;
    }

    function setColumnFactory(\CdiDataGrid\Factory\ColumnFactory $columnFactory) {
        $this->columnFactory = $columnFactory;
    }

    //<--COLUMNS

    public function prepare() {

        if (!isset($this->source)) {
            throw new \CdiDataGrid\Exception\SourceException();
        }

        //CRUD - to review 
        $this->processCrudActions();
        //SEE IF CAN RETURN HERE
        //CRUD CONFIGURE
        $this->crudConfigure();

        //Extract and generate source columns
        $this->buildColumns();

        //Filters
        $this->generateFormFilters();
        $this->buildFilters();
        $this->getSource()->setFilters($this->getFilters());

        //Order (SORT)
        $this->prepareSort();

        //Paginator
        $this->preparePaginator();

        //Data
        $this->data = $this->paginator->getCurrentItems();
        $this->processData();

        //Extra Columns (todo)
        $this->mergeExtraColumn();

        //Order Columns..Need review to enable
        //$this->processOrderColumn();

        $this->ready = true;
    }

    protected function crudConfigure() {
        if ($this->getOptions()->getCrudConfig()["enable"] === true) {
            $this->addCrudColumn("", "left", $this->getOptions()->getCrudConfig());
        }
    }

    protected function preparePaginator() {
        $this->paginatorAdapter = $this->getSource()->execute();
        $this->paginator = new Paginator($this->paginatorAdapter);
        $this->paginator->setDefaultItemCountPerPage($this->getOptions()->getRecordsPerPage());
        $this->paginator->setCurrentPageNumber($this->getPage());
    }

    public function getForm() {
        return $this->getSource()->getForm();
    }

    //-->CRUD

    public function getCrudForm() {
        return $this->getCrud()->getCrudForm();
    }

    protected function processCrudActions() {
        if ($this->getCrud()->crudActions()) {
            $this->setInstanceToRender($this->getCrud()->getInstanceToRender());
        }
    }

    function getCrud() {
        if (!isset($this->crud)) {
            $this->crud = new \CdiDataGrid\Crud($this->source, $this->getPost());
        }
        return $this->crud;
    }

    function setCrud($crud) {
        $this->crud = $crud;
    }

    public function addCrudColumn($name = "", $side = "left", $crudConfig = []) {
        $column = new CrudColumn($name, $side, $crudConfig, $this->id);
        $column->setFilterActive(false);
        if ($side == "left") {
            array_unshift($this->extraColumns, $column);
        } else if ($side == "right") {
            array_push($this->extraColumns, $column);
        }
    }

    //<--CRUD
    //
    //
    //-->MVCEVENT
    public function getRoute() {
        return $this->getMvcevent()->getRouteMatch()->getMatchedRouteName();
    }

    public function getQuery() {
        return $this->getMvcevent()->getRequest()->getQuery();
    }

    public function getPost() {
        return array_merge_recursive(
                $this->getMvcevent()->getRequest()->getPost()->toArray(), $this->getMvcevent()->getRequest()->getFiles()->toArray()
        );
    }

    public function getPage() {
        if (!$this->page) {
            $page = $this->getMvcevent()->getRequest()->getQuery('page');
            if ($page) {
                $this->setPage($page);
            } else {
                $this->setPage(1);
            }
        }
        return $this->page;
    }

    public function setPage($page) {
        $this->page = $page;
    }

    public function getQueryArray() {
        $query = $this->getQuery();
        $return = array();
        foreach ($query as $key => $value) {
            $return[$key] = $value;
        }
        return $return;
    }

    //<--MVCEVENT


    public function prepareSort() {
        $query = $this->getQuery();
        if ($query["sortBy"] && $query["sortDirection"]) {

            $column = $this->columns[$query["sortBy"]];

            $this->sort = new \CdiDataGrid\Sort();
            $this->sort->setBy($query["sortBy"]);
            $this->sort->setDirection($query["sortDirection"]);
            $this->sort->setColumn($column);
            $this->source->setSort($this->sort);
        }
    }

    //-->FILTERS

    public function buildFilters() {
        $this->filters = new \CdiDataGrid\Filters();
        if (count($this->getQuery())) {
            foreach ($this->getQuery() as $key => $value) {
                $name = str_replace("f_", "", $key);
                if ($value != "") {
                    if (key_exists($name, $this->columns)) {
                        $filter = new \CdiDataGrid\Filter($this->columns[$name], $value);
                        $this->filters->addFilter($filter);
                    }
                }
            }
        }
    }

    protected function generateFormFilters() {
        $this->formFilters = $this->getFormFilterFactory()->create(clone $this->source->getForm(), $this->getPage(), $this->getQuery());
    }

    function getFormFilterFactory() {
        if (!isset($this->formFilterFactory)) {
            $this->setFormFilterFactory(new Factory\FormFilterFactory($this->id));
        }
        return $this->formFilterFactory;
    }

    function setFormFilterFactory(\CdiDataGrid\Factory\FormFilterFactory $formFilterFactory) {
        $this->formFilterFactory = $formFilterFactory;
    }

    public function getFormFilters() {
        return $this->formFilters;
    }

    public function setFormFilters($formFilters) {
        $this->formFilters = $formFilters;
    }

    function getFilters() {
        return $this->filters;
    }

    function setFilters(type $filters) {
        $this->filters = $filters;
    }

    //<--FILTERS
    //
    //-->ORDER COLUMNS (REVIEW-CONFIG)
    public function setOrderColumn($column, $order) {
        $this->OrderColumnCollection[$column] = $order;
    }

    public function processOrderColumn() {
        asort($this->OrderColumnCollection);
        $newOrder = array();
        foreach ($this->OrderColumnCollection as $key => $order) {

            foreach ($this->columns as $keyColumn => $objColumn) {
                if ($key == $objColumn->getName()) {
                    $newOrder[$order] = $objColumn;
                    unset($this->columnCollection[$keyColumn]);
                }
            }
        }
        $this->columns = array_merge($newOrder, $this->columns);
    }

    //<--ORDER COLUMNS (REVIEW-CONFIG)

    protected function processData() {

        foreach ($this->data as $record) {
            if (is_array($record)) {
                $this->row[] = $record;
            } else if (is_object($record)) {
                if ($record instanceof \stdClass) {
                    $this->row[] = (array) $record;
                } else {
                    //Process Data Columns
                    foreach ($this->columns as $column) {
                        $method = "get" . ucfirst($column->getName());
                        $item[$column->getName()] = $record->$method();
                    }
                    //Process Data ExtraColumns
                    foreach ($this->extraColumns as $ExtraColumn) {
                        $item[$ExtraColumn->getName()] = $ExtraColumn->processData($item);
                    }
                    $this->row[] = $item;
                }
            }
        }
    }

    //EXTRA COLUMNS - TO REVIEW
    public function addExtraColumn($name, $originalValue, $side = "left", $filter = false) {
        
        if (key_exists($name, $this->getColumnsConfig())) {
            $columnConfig = $this->getColumnsConfig()[$name];
        } else {
            $columnConfig = array();
        }
        $columnConfig["type"] ="extra";
        $extraColumn = $this->getColumnFactory()->create($name, $columnConfig);
        
        $extraColumn->setOriginalValue($originalValue);
        $extraColumn->setFilterActive($filter);

        if ($side != "left" && $side != "right") {
            throw new Exception("Side must be 'left' or 'right'");
        }

        if ($side == "left") {
            $extraColumn->setSide("left");
            array_unshift($this->extraColumns, $extraColumn);
        } else if ($side == "right") {
            $extraColumn->setSide("right");
            array_push($this->extraColumns, $extraColumn);
        }
    }

    protected function mergeExtraColumn() {
        foreach ($this->extraColumns as $ExtraColumn) {
            if ($ExtraColumn->getSide() == "left") {
                array_unshift($this->columns, $ExtraColumn);
            } else if ($ExtraColumn->getSide() == "right") {
                array_push($this->columns, $ExtraColumn);
            }
        }
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = str_replace (' ', '', $id);
    }

    public function getRow() {
        return $this->row;
    }

    public function getTableClass() {
        return $this->tableClass;
    }

    public function setTableClass($tableClass) {
        $this->tableClass = $tableClass;
    }

    function getInstanceToRender() {
        return $this->instanceToRender;
    }

    function setInstanceToRender($instanceToRender) {
        $this->instanceToRender = $instanceToRender;
    }

    function getRenderTemplate() {
        return $this->renderTemplate;
    }

    function setRenderTemplate($renderTemplate) {
        $this->renderTemplate = $renderTemplate;
    }

    function getAddBtn() {
        return $this->addBtn;
    }

    function setAddBtn($addBtn) {
        $this->addBtn = $addBtn;
    }

    function getColumnFilter() {
        return $this->columnFilter;
    }

    function setColumnFilter($columnFilter) {
        $this->columnFilter = $columnFilter;
    }

    function getColumnOrder() {
        return $this->columnOrder;
    }

    function setColumnOrder($columnOrder) {
        $this->columnOrder = $columnOrder;
    }

    function getRecordDetail() {
        return $this->crud->getRecord();
    }

    function setRecordDetail($recordDetail) {
        $this->recordDetail = $recordDetail;
    }

    function getOrderBy() {
        return $this->orderBy;
    }

    function getOrderDirection() {
        return $this->orderDirection;
    }

    /**
     * @return string $template
     */
    function getTemplate() {
        return $this->template;
    }

    /**
     * @param string $template 
     */
    function setTemplate($template) {
        $this->template = $template;
    }

    function getPaginator() {
        return $this->paginator;
    }

    function setPaginator(\Zend\Paginator\Paginator $paginator) {
        $this->paginator = $paginator;
    }

    function getColumns() {
        return $this->columns;
    }

    function getCrudColumn() {
        return $this->crudColumn;
    }

    function setCrudColumn($crudColumn) {
        $this->crudColumn = $crudColumn;
    }

    //TOREVIEW
    /**
     * Compatibilidad
     */
    public function setFormFilterSelect($key, \Zend\Form\Element\Select $element) {
        $this->forceFilters[$key] = $element;
    }

    public function __toString() {
        return "toStringGrid";
    }

}
