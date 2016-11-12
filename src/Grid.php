<?php

namespace CdiDataGrid;

use \Zend\Paginator\Paginator;
use CdiDataGrid\DataGrid\Column\ExtraColumn;

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
     * Template a renderzar.
     * 
     * @var string
     */
    protected $template = "default";

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
     * Columns config
     *
     * @var Array
     */
    protected $columnConfig = array();

    /**
     * A factory for columns
     *
     * @var \CdiDataGrid\Factory\ColumnFactory
     */
    protected $columnFactory;

    /**
     * A columns collection
     * 
     * @var array
     */
    protected $columns = array();

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
    //TOREVIEW

    protected $orderBy;
    protected $orderDirection;
    protected $columnsName = array();
    protected $OrderColumnCollection = array();
    protected $extraColumnCollection = array();
    protected $selectFilterCollection = array();
    protected $renderOk = false;
    protected $options;
    protected $expRegData = "/\{\{\w*\}\}/";
    protected $formFilters;
    protected $editForm = null;
    protected $tableClass;
    protected $optionDelete = false;
    protected $optionEdit = false;
    protected $optionAdd = false;
    protected $optionView = false;
    protected $addBtn = null;
    protected $recordDetail;
    protected $forceFilters = array();

    public function __construct(\Zend\Mvc\MvcEvent $mvcevent) {

        $this->mvcevent = $mvcevent;

        /* @var $request \Zend\Http\Request */
        $request = $this->mvcevent->getRequest();

        /* @var $routematch \Zend\Router\RouteMatch */
        $routeMatch = $this->mvcevent->getRouteMatch();

        $this->setRequest($request);
        $this->setRouteMatch($routeMatch);
    }

    function getColumnConfig() {
        return $this->columnConfig;
    }

    function setColumnConfig(Array $columnConfig) {
        $this->columnConfig = $columnConfig;
    }

    protected function buildColumns() {
        $sourceColumnsName = $this->getSource()->pullColumns();

        foreach ($sourceColumnsName as $name) {
            $this->createColumn($name);
        }
    }

    protected function createColumn($name) {
        if (key_exists($name, $this->columnConfig)) {
            $columnConfig = $this->columnConfig[$name];
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

    public function prepare() {

        if (!isset($this->source)) {
            throw new \CdiDataGrid\Exception\SourceException();
        }

        //CRUD - to review
        $this->verifyCrudActions();

        //Extract and generate source columns
        $this->buildColumns();

        //Filters
        $this->generateFormFilters();
        $this->buildFilters();
        $this->getSource()->setFilters($this->getFilters());

        //Order (SORT)
        $this->prepareOrder();
        $this->getSource()->setOrder($this->orderBy, $this->orderDirection);

        //Paginator
        $this->preparePaginator();

        //Data
        $this->data = $this->paginator->getCurrentItems();
        $this->processData();

        //Extra Columns (todo)
        $this->mergeExtraColumn();

        //Order again? (To review)
        $this->processOrderColumn();
    }

    protected function preparePaginator() {
        $this->paginatorAdapter = $this->getSource()->execute();
        $this->paginator = new Paginator($this->paginatorAdapter);
        $this->paginator->setDefaultItemCountPerPage($this->getOptions()->getRecordsPerPage());
        $this->paginator->setCurrentPageNumber($this->getPage());
    }

    

    public function getEntityForm() {
        return $this->getSource()->getEntityForm();
    }

    public function addDelOption($name, $side, $btnClass, $btnVal = null) {
        $this->setOptionDelete(true);
        $originalValue = "<i class='" . $btnClass . "' onclick='cdiDeleteRecord({{id}})'>" . $btnVal . "</i>";
        $column = new ExtraColumn($name, $side);
        $column->setOriginalValue($originalValue);
        $column->setFilterActive(false);
        if ($side == "left") {
            array_unshift($this->extraColumnCollection, $column);
        } else if ($side == "right") {
            array_push($this->extraColumnCollection, $column);
        }
    }

    public function addEditOption($name, $side, $btnClass, $btnVal = null) {
        $this->setOptionEdit(true);
        $originalValue = "<i class='" . $btnClass . "' onclick='cdiEditRecord({{id}})'>" . $btnVal . "</i>";
        $column = new ExtraColumn($name, $side);
        $column->setOriginalValue($originalValue);
        $column->setFilterActive(false);
        if ($side == "left") {
            array_unshift($this->extraColumnCollection, $column);
        } else if ($side == "right") {
            array_push($this->extraColumnCollection, $column);
        }
    }

    public function addViewOption($name, $side, $btnClass, $btnVal = null) {
        $this->setOptionEdit(true);
        $originalValue = "<i class='" . $btnClass . "' onclick='cdiViewRecord({{id}})'>" . $btnVal . "</i>";
        $column = new ExtraColumn($name, $side);
        $column->setOriginalValue($originalValue);
        $column->setFilterActive(false);
        if ($side == "left") {
            array_unshift($this->extraColumnCollection, $column);
        } else if ($side == "right") {
            array_push($this->extraColumnCollection, $column);
        }
    }

    public function addNewOption($name, $btnClass, $btnVal = "+") {
        $this->setOptionAdd(true);
        $this->addBtn["name"] = $name;
        $this->addBtn["class"] = $btnClass;
        $this->addBtn["value"] = $btnVal;
    }

    public function getAllData() {
        return $this->getSource()->getAllData($this->limitQuery);
    }

    protected function mergeExtraColumn() {
        foreach ($this->extraColumnCollection as $ExtraColumn) {
            if ($ExtraColumn->getSide() == "left") {
                array_unshift($this->columnCollection, $ExtraColumn);
            } else if ($ExtraColumn->getSide() == "right") {
                array_push($this->columnCollection, $ExtraColumn);
            }
        }
    }

    public function getRoute() {
        return $this->getRouteMatch()->getMatchedRouteName();
    }

    public function getQuery() {
        return $this->getRequest()->getQuery();
    }

    public function getPost() {
        return array_merge_recursive(
                $this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray()
        );
    }

    public function getQueryArray() {
        $query = $this->getQuery();
        $return = array();
        foreach ($query as $key => $value) {
            $return[$key] = $value;
        }
        return $return;
    }

    public function prepareOrder() {
        $query = $this->getQuery();
        $order = $query["orderBy"];
        $orderDirection = $query["orderDirection"];
        if ($order && $orderDirection) {
            $this->orderBy = $order;
            $this->orderDirection = $orderDirection;
        }
    }

    public function buildFilters() {
        $query = $this->getQuery();
        $this->filters = new \CdiDataGrid\Filter\Filters();
        $match = false;
        if (count($query)) {
            foreach ($query as $key => $value) {
                $name = str_replace("f_", "", $key);

                $match = false;
                if ($value != "") {
                    if(key_exists($name, $this->columns)){

                            $filter = new \CdiDataGrid\Filter\Filter($this->columns[$name],$value);
                            $this->filters->addFilter($filter);
                        
                    }
                }
            }
        }
    }

    protected function generateFormFilters() {
        $this->formFilters = $this->source->getBasicForm();

        $this->formFilters->setName('GridFormFilters');
        $this->formFilters->setAttribute('method', 'get');

        foreach ($this->formFilters as $key => $element) {
            if (preg_match("/hidden/i", $element->getAttribute("type")) && $element->getName() == 'id') {
                $newElement = new \Zend\Form\Element\Text('id');
                $this->formFilters->remove($element->getName());
                $this->formFilters->add($newElement);
            }


            if (preg_match("/textarea/i", $element->getAttribute("type"))) {
                $name = $element->getName();
                $newElement = new \Zend\Form\Element\Text($name);
                $this->formFilters->remove($element->getName());
                $this->formFilters->add($newElement);
            }

            if (preg_match("/number/i", $element->getAttribute("type"))) {
                $name = $element->getName();
                $newElement = new \Zend\Form\Element\Text($name);
                $this->formFilters->remove($element->getName());
                $this->formFilters->add($newElement);
            }

            if (preg_match("/checkbox/i", $element->getAttribute("type"))) {
                $name = $element->getName();

                $newElement = new \Zend\Form\Element\Select($name);
                $newElement->setOptions(array(
                    'value_options' => array(0 => "false", 1 => "true"),
                    'empty_option' => $name
                ));

                $this->formFilters->remove($element->getName());
                $this->formFilters->add($newElement);
            }
        }


        foreach ($this->forceFilters as $key => $element) {
            if ($this->formFilters->has($key)) {
                $this->formFilters->remove($key);
            }
            $this->formFilters->add($element);
        }

        $this->formFilters->add(array(
            'name' => 'page',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPage()
            )
        ));

        $this->formFilters->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Filter'
            )
        ));

        $request = $this->getRequest()->getQuery();

        $this->formFilters->setData($request);
    }

    public function customHelperColumn($columnName, $helperName, $customData = null) {
        $this->customHelperColumnCollection[$columnName]["helper"] = $helperName;
        $this->customHelperColumnCollection[$columnName]["customData"] = $customData;
    }

    protected function processCustomHelperColumn() {
        foreach ($this->columns as &$column) {
            if (key_exists($column->getName(), $this->customHelperColumnCollection)) {
                $column->setHelper($this->customHelperColumnCollection[$column->getName()]["helper"]);
                $column->setCustomData($this->customHelperColumnCollection[$column->getName()]["customData"]);
                $column->setType("custom");
            }
        }
    }

    protected function processData() {

        foreach ($this->data as $record) {

            if (is_array($record)) {
                $this->row[] = $record;
            } else if (is_object($record)) {

                if ($record instanceof \stdClass) {
                    $this->row[] = (array) $record;
                } else {
                    foreach ($this->columns as $column) {

                        if (is_a($column, "\CdiDataGrid\DataGrid\Column\ExtraColumn")) {
                            
                        }

                        $method = "get" . ucfirst($column->getName());
                        $item[$column->getName()] = $record->$method();
                    }

                    foreach ($this->extraColumnCollection as $ExtraColumn) {
                        $valueExtraColumn = $this->processDataExtraColumn($ExtraColumn, $item);
                        $item[$ExtraColumn->getName()] = $valueExtraColumn;
                    }

                    $this->row[] = $item;
                }
            }
        }
    }

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

    public function addExtraColumn($name, $originalValue, $side = "left", $filter = false) {
        $column = new ExtraColumn($name, $side);

        $column->setOriginalValue($originalValue);
        $column->setFilterActive($filter);

        if ($side == "left") {
            array_unshift($this->extraColumnCollection, $column);
        } else if ($side == "right") {
            array_push($this->extraColumnCollection, $column);
        }
    }

    protected function processDataExtraColumn(\CdiDataGrid\DataGrid\Column\ExtraColumn $ExtraColumn, $row) {

        $originalValue = $ExtraColumn->getOriginalValue();

        if (preg_match_all($this->expRegData, $originalValue, $matches)) {
            $result = $originalValue;
            foreach ($matches[0] as $match) {
                $fieldName = preg_replace("/\{|\}/", "", $match);
                $replace = $row[$fieldName];
                $result = str_replace($match, $replace, $result);
            }
        } else {
            $result = $originalValue;
        }

        return $result;
    }

    public function __toString() {
        return "toStringGrid";
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getRenderOk() {
        return $this->renderOk;
    }

    public function getOptions() {
        return $this->options;
    }

    public function setOptions(\CdiDataGrid\Options\GridOptionsInterface $options) {
        $this->options = $options;
    }

    public function getPage() {
        if (!$this->page) {
            $page = $this->getRequest()->getQuery('page');
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

    public function getRecordPerPage() {
        return $this->recordPerPage;
    }

    public function setRecordsPerPage($recordsPerPage) {
        $this->getOptions()->setRecordsPerPage($recordsPerPage);
    }

    public function getFormFilters() {
        return $this->formFilters;
    }

    public function setFormFilters($formFilters) {
        $this->formFilters = $formFilters;
    }

    public function getRow() {
        return $this->row;
    }

    public function getLimitQuery() {
        return $this->limitQuery;
    }

    public function setLimitQuery($limitQuery) {
        $this->limitQuery = $limitQuery;
    }

    public function getTableClass() {
        return $this->tableClass;
    }

    public function setTableClass($tableClass) {
        $this->tableClass = $tableClass;
    }

    function getOptionDelete() {
        return $this->optionDelete;
    }

    function getOptionEdit() {
        return $this->optionEdit;
    }

    function getOptionAdd() {
        return $this->optionAdd;
    }

    function setOptionDelete($optionDelete) {
        $this->optionDelete = $optionDelete;
    }

    function setOptionEdit($optionEdit) {
        $this->optionEdit = $optionEdit;
    }

    function setOptionAdd($optionAdd) {
        $this->optionAdd = $optionAdd;
    }

    function getEditForm() {
        return $this->editForm;
    }

    function setEditForm($editForm) {
        $this->editForm = $editForm;
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
        return $this->recordDetail;
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

    //TOREVIEW
    /**
     * Compatibilidad
     */
    public function setFormFilterSelect($key, \Zend\Form\Element\Select $element) {
        $this->forceFilters[$key] = $element;
    }
    
    protected function verifyCrudActions() {
        $aData = $this->getPost();


        if (isset($aData["crudAction"])) {

            if ($aData["crudAction"] == 'delete') {

                $return = $this->getSource()->delRecord($aData["crudId"]);
                return $return;
            }

            if ($aData["crudAction"] == 'view') {
                $this->setInstanceToRender("detail");
                $this->recordDetail = $this->getSource()->viewRecord($aData["crudId"]);
                return true;
            }

            if ($aData["crudAction"] == 'edit') {
                $this->getSource()->generateEntityForm($aData["crudId"]);
                $this->getEntityForm()->add(array(
                    'name' => 'crudAction',
                    'type' => 'Zend\Form\Element\Hidden',
                    'attributes' => array(
                        'value' => 'submitEdit'
                    )
                ));
                $this->getEntityForm()->add(array(
                    'name' => 'crudId',
                    'type' => 'Zend\Form\Element\Hidden',
                    'attributes' => array(
                        'value' => $aData["crudId"]
                    )
                ));
                $this->setInstanceToRender("formEntity");
                return $return;
            }



            if ($aData["crudAction"] == 'submitEdit') {

                $result = $this->getSource()->updateRecord($aData["crudId"], $aData);

                $this->getEntityForm()->add(array(
                    'name' => 'crudAction',
                    'type' => 'Zend\Form\Element\Hidden',
                    'attributes' => array(
                        'value' => 'submitEdit'
                    )
                ));
                $this->getEntityForm()->add(array(
                    'name' => 'crudId',
                    'type' => 'Zend\Form\Element\Hidden',
                    'attributes' => array(
                        'value' => $aData["crudId"]
                    )
                ));


                if (!$result) {
                    $this->setInstanceToRender("formEntity");
                }


                //Maybe a Redirect
            }


            if ($aData["crudAction"] == 'add') {
                $this->getSource()->generateEntityForm(null);
                $this->getEntityForm()->add(array(
                    'name' => 'crudAction',
                    'type' => 'Zend\Form\Element\Hidden',
                    'attributes' => array(
                        'value' => 'submitAdd'
                    )
                ));

                $this->setInstanceToRender("formEntity");
                return $return;
            }

            if ($aData["crudAction"] == 'submitAdd') {

                $result = $this->getSource()->saveRecord($aData);

                $this->getEntityForm()->add(array(
                    'name' => 'crudAction',
                    'type' => 'Zend\Form\Element\Hidden',
                    'attributes' => array(
                        'value' => 'submitAdd'
                    )
                ));

                if (!$result) {
                    $this->setInstanceToRender("formEntity");
                } 
            }
        }
    }

    //OK

    function getRequest() {
        return $this->request;
    }

    function setRequest(\Zend\Http\Request $request) {
        $this->request = $request;
    }

    function getRouteMatch() {
        return $this->routeMatch;
    }

    function setRouteMatch(\Zend\Router\RouteMatch $routeMatch) {
        $this->routeMatch = $routeMatch;
    }

    function getPaginator() {
        return $this->paginator;
    }

    function setPaginator(\Zend\Paginator\Paginator $paginator) {
        $this->paginator = $paginator;
    }

    function getSource() {
        return $this->source;
    }

    function setSource(\CdiDataGrid\Source\SourceInterface $source) {
        $this->source = $source;
    }

    function getColumns() {
        return $this->columns;
    }

    function getFilters() {
        return $this->filters;
    }

    function setFilters(type $filters) {
        $this->filters = $filters;
    }

}
