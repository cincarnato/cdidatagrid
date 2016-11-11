<?php

namespace CdiDataGrid;

use \Zend\Paginator\Paginator;
use CdiDataGrid\DataGrid\Column\Column;
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
     * Column Manager
     * 
     * @var \CdiDataGrid\ColumnManager
     */
    protected $cm;

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
     * An Array with columns config
     * 
     * @var Array
     */
    protected $columnsConfig;

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
    protected $filters;
    protected $orderBy;
    protected $orderDirection;
    protected $columnsName = array();
    protected $columnCollection = array();
    protected $OrderColumnCollection = array();
    protected $extraColumnCollection = array();
    protected $selectFilterCollection = array();
    protected $classTdColumnCollection = array();
    protected $recordPerPage = 10;
    protected $renderOk = false;
    protected $options;
    protected $expRegData = "/\{\{\w*\}\}/";
    protected $formFilters;
    protected $editForm = null;
    protected $exportCsv = false;
    protected $csvForm;
    protected $csvCommaOn = false;
    protected $csvSemicolonOn = false;
    protected $csvTabulatorOn = false;
    protected $limitQuery = null;
    protected $tableClass;
    protected $optionDelete = false;
    protected $optionEdit = false;
    protected $optionAdd = false;
    protected $optionView = false;
    protected $addBtn = null;
    protected $instanceToRender = "grid";
    protected $renderTemplate = "bootstrap";
    protected $columnFilter = true;
    protected $columnOrder = true;
    protected $recordDetail;
    protected $forceFilters = array();
    //Columns
    protected $renameColumnCollection = array();
    protected $hiddenColumnCollection = array();
    protected $tooltipColumnCollection = array();
    protected $linkColumnCollection = array();
    protected $longTextColumnCollection = array();
    protected $booleanColumnCollection = array();
    protected $fileColumnCollection = array();
    protected $datetimeColumnCollection = array();
    protected $customHelperColumnCollection = array();
    protected $aditionalHtmlColumnCollection = array();

    public function __construct(\Zend\Http\Request $request, \Zend\Router\RouteMatch $routeMatch) {
        $this->setRequest($request);
        $this->setRouteMatch($routeMatch);
        $this->setCm(new ColumnManager);
    }
    
    function getColumnsConfig() {
        return $this->columnsConfig;
    }

    function setColumnsConfig(Array $columnsConfig) {
        $this->columnsConfig = $columnsConfig;
    }

    
    /**
     * Set FormatOptions object for current column
     *
     * @param $formatOptions FormatOptions object for current column
     * @return \SynergyDataGrid\Grid\Column
     */
    public function addForceFilter($key, $element) {
        $this->forceFilters[$key] = $element;
    }

    /**
     * Compatibilidad
     */
    public function setFormFilterSelect($key, \Zend\Form\Element\Select $element) {
        $this->forceFilters[$key] = $element;
    }

    public function prepare() {

        if (!isset($this->source)) {
            throw new \CdiDataGrid\Exception\SourceException();
        }

        $returnCrud = $this->verifyCrudActions();

        $this->extractColumns();

        $this->processFormFilters();
        $this->prepareFilters();

        $this->prepareOrder();
        $this->getSource()->setFilters($this->filters);
        $this->getSource()->setOrder($this->orderBy, $this->orderDirection);

        //Paginator
        $this->preparePaginator();
        
        //Data
        $this->data = $this->paginator->getCurrentItems();

        $this->processData();
        $this->mergeExtraColumn();

        $this->processRenameColumns();
        $this->processHiddenColumns();
        $this->processFileColumns();
        $this->processTooltipColumns();
        $this->processBooleanColumns();
        $this->processDatetimeColumns();
        $this->processLinkColumns();
        $this->processLongTextColumns();
        $this->processAditionalHtmlColumns();
        $this->processCustomHelperColumn();

        $this->processOrderColumn();

        $export = $this->exportCsv();
        return $this->export;
    }

    protected function preparePaginator() {
        $this->setPaginator(new Paginator($this->getSource()->getPaginatorAdapter()));
        $this->getPaginator()->setDefaultItemCountPerPage($this->getOptions()->getRecordsPerPage());
        $this->getPaginator()->setCurrentPageNumber($this->getPage());
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
                } else {
                    //$plugin = $this->getServiceLocator()->get('CdiDatagridRefresh');
                }
            }
        }
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

    public function csvForm() {
        
    }

    protected function exportCsv() {
        $query = $this->getQuery();
        if ($query["cdiExportCommaCsv"] == "yes") {
            $csv = new \CdiDataGrid\DataGrid\Renderer\Rcsv();
            $this->export = $csv->deploy($this, ",");
            return $this->export;
        }
        if ($query["cdiExportSemiColonCsv"] == "yes") {
            $csv = new \CdiDataGrid\DataGrid\Renderer\Rcsv();
            $this->export = $csv->deploy($this, ";");
            return $this->export;
        }
        if ($query["cdiExportTabulatorCsv"] == "yes") {
            $csv = new \CdiDataGrid\DataGrid\Renderer\Rcsv();
            $this->export = $csv->deploy($this, "\t");
            return $this->export;
        }

        if ($query["csvExport"] == "yes") {
            $separator = array("coma" => ",", "puntoycoma" => ";", "tabulador" => "\t");
            $csv = new \CdiDataGrid\DataGrid\Renderer\Rcsv();

            $this->export = $csv->deploy($this, $separator[$query["separatorCsv"]], $query["nameCsv"]);
            return $this->export;
        }

        return false;
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
        //  return $this->getRequest()->getPost();
        //se agrega para file
        $post = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray()
        );
        return $post;
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

    public function prepareFilters() {
        $query = $this->getQuery();
        $filters = array();
        $match = false;
        if (count($query)) {
            foreach ($query as $key => $value) {
                $name = str_replace("f_", "", $key);

                $match = false;
                if ($value != "") {
                    foreach ($this->columnCollection as $column) {



                        if ($column->getName() == $name) {

                            if (preg_match("/select/i", $this->formFilters->get($name)->getAttribute("type"))) {
                                $match = true;
                                $type = "eq";
                                $value = $value;
                            }

                            if (preg_match("/^==/", $value)) {
                                $match = true;
                                $type = "eq";
                                $value = str_replace("==", "", $value);
                            }

                            if (preg_match("/^</", $value)) {
                                $match = true;
                                $type = "lt";
                                $value = str_replace("<", "", $value);
                            }

                            if (preg_match("/^>/", $value)) {
                                $match = true;
                                $type = "gt";
                                $value = str_replace(">", "", $value);
                            }

                            if (preg_match("/></", $value)) {
                                $match = true;
                                $type = "between";
                                $values = explode("><", $value);
                                $value = $values[0];
                                $value2 = $values[1];
                            }

                            if (!$match) {
                                $type = "like";
                            }


                            $filter = array(
                                'key' => $column->getName(),
                                'value' => $value,
                                'type' => $type,
                                'value2' => $value2
                            );
                            array_push($filters, $filter);
                        }
                    }
                }
            }
        }
        $this->filters = $filters;
    }

    protected function processFormFilters() {
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
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->customHelperColumnCollection)) {
                $column->setHelper($this->customHelperColumnCollection[$column->getName()]["helper"]);
                $column->setCustomData($this->customHelperColumnCollection[$column->getName()]["customData"]);
                $column->setType("custom");
            }
        }
    }

    public function hiddenColumn($columnName, $showFilter = true) {
        $this->hiddenColumnCollection[$columnName] = $showFilter;
    }

    public function datetimeColumn($columnName, $format) {
        $this->datetimeColumnCollection[$columnName] = $format;
    }

    public function tooltipColumn($columnName, $tooltip) {
        $this->tooltipColumnCollection[$columnName] = $tooltip;
    }

    public function linkColumn($columnName) {
        $this->linkColumnCollection[$columnName] = $columnName;
    }

    public function classTdColumn($columnName, $class) {
        $this->classTdColumnCollection[$columnName] = $class;
    }

    public function longTextColumn($columnName, $length = 15) {
        $this->longTextColumnCollection[$columnName] = $length;
    }

    public function aditionalHtmlColumn($columnName, $HtmlBegin, $HtmlEnd) {
        $this->aditionalHtmlColumnCollection[$columnName]["begin"] = $HtmlBegin;
        $this->aditionalHtmlColumnCollection[$columnName]["end"] = $HtmlEnd;
    }

    public function booleanColumn($columnName, $replaceTrueBy, $replaceFalseBy) {

        $this->booleanColumnCollection[$columnName]["true"] = $replaceTrueBy;
        $this->booleanColumnCollection[$columnName]["false"] = $replaceFalseBy;
    }

    public function fileColumn($columnName, $path, $width = "100%", $height = "100%") {
        $this->fileColumnCollection[$columnName]["path"] = $path;
        $this->fileColumnCollection[$columnName]["width"] = $width;
        $this->fileColumnCollection[$columnName]["height"] = $height;
    }

    protected function processData() {

        foreach ($this->data as $record) {
            if (is_array($record)) {
                $this->row[] = $record;
            } else if (is_object($record)) {

                if ($record instanceof \stdClass) {
                    $this->row[] = (array) $record;
                } else {
                    foreach ($this->columnCollection as $column) {

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

    protected function extractColumns() {
        $fieldNames = $this->getSource()->getColumns();
        foreach ($fieldNames as $name) {
            $column = new Column($name);
            $this->columnCollection[$name] = $column;
        }
    }

    public function setOrderColumn($column, $order) {


        $this->OrderColumnCollection[$column] = $order;
    }

    public function processOrderColumn() {

        asort($this->OrderColumnCollection);
        $newOrder = array();
        foreach ($this->OrderColumnCollection as $key => $order) {

            foreach ($this->columnCollection as $keyColumn => $objColumn) {
                if ($key == $objColumn->getName()) {
                    $newOrder[$order] = $objColumn;
                    unset($this->columnCollection[$keyColumn]);
                }
            }
        }
        $this->columnCollection = array_merge($newOrder, $this->columnCollection);
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

    public function changeColumnName($ColumnName, $NewColumnName) {
        $this->renameColumnCollection[$ColumnName] = $NewColumnName;
    }

    protected function processRenameColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->renameColumnCollection)) {
                $column->setVisualName($this->renameColumnCollection[$column->getName()]);
            }
        }
    }

    protected function processHiddenColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->hiddenColumnCollection)) {
                $column->setHidden(true);
            }
        }
    }

    protected function processDatetimeColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->datetimeColumnCollection)) {
                $column->setType("datetime");
                $column->setFormatDatetime($this->datetimeColumnCollection[$column->getName()]);
            }
        }
    }

    protected function processLinkColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->linkColumnCollection)) {
                $column->setType("link");
            }
        }
    }

    protected function processFileColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->fileColumnCollection)) {
                $column->setType("file");
                $column->setFilePath($this->fileColumnCollection[$column->getName()]["path"]);
                $column->setFileWidth($this->fileColumnCollection[$column->getName()]["width"]);
                $column->setFileHeight($this->fileColumnCollection[$column->getName()]["height"]);
            }
        }
    }

    protected function processLongTextColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->longTextColumnCollection)) {
                $column->setType("longText");
                $column->setLength($this->longTextColumnCollection[$column->getName()]);
            }
        }
    }

    protected function processTooltipColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->tooltipColumnCollection)) {
                $column->setTooltip($this->tooltipColumnCollection[$column->getName()]);
            }
        }
    }

    protected function processAditionalHtmlColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->aditionalHtmlColumnCollection)) {
                $column->setHtmlBegin($this->aditionalHtmlColumnCollection[$column->getName()]["begin"]);
                $column->setHtmlEnd($this->aditionalHtmlColumnCollection[$column->getName()]["end"]);
            }
        }
    }

    protected function processBooleanColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->booleanColumnCollection)) {
                $column->setType("boolean");
                $column->setReplaceTrueBy($this->booleanColumnCollection[$column->getName()]["true"]);
                $column->setReplaceFalseBy($this->booleanColumnCollection[$column->getName()]["false"]);
            }
        }
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

    /*
     * Compatibilidad
     */

    public function setRecordPerPage($recordsPerPage) {
        $this->getOptions()->setRecordsPerPage($recordsPerPage);
    }

    public function getColumnCollection() {
        return $this->columnCollection;
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

    public function getCsvCommaOn() {
        return $this->csvCommaOn;
    }

    public function setCsvCommaOn($csvCommaOn) {
        $this->csvCommaOn = $csvCommaOn;
    }

    public function getCsvSemicolonOn() {
        return $this->csvSemicolonOn;
    }

    public function setCsvSemicolonOn($csvSemicolonOn) {
        $this->csvSemicolonOn = $csvSemicolonOn;
    }

    public function getLimitQuery() {
        return $this->limitQuery;
    }

    public function setLimitQuery($limitQuery) {
        $this->limitQuery = $limitQuery;
    }

    public function getCsvTabulatorOn() {
        return $this->csvTabulatorOn;
    }

    public function setCsvTabulatorOn($csvTabulatorOn) {
        $this->csvTabulatorOn = $csvTabulatorOn;
    }

    public function getTableClass() {
        return $this->tableClass;
    }

    public function setTableClass($tableClass) {
        $this->tableClass = $tableClass;
    }

    public function getExportCsv() {
        return $this->exportCsv;
    }

    public function setExportCsv($exportCsv) {
        $this->exportCsv = $exportCsv;
    }

    public function getCsvForm() {
        if (!$this->csvForm) {
            $this->csvForm = new \CdiDataGrid\Form\Csv();
        }

        return $this->csvForm;
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

    public function getClassTdColumn($columnName) {
        return $this->classTdColumnCollection[$columnName];
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
    
    function getCm() {
        return $this->cm;
    }

    function setCm(\CdiDataGrid\ColumnManager $cm) {
        $this->cm = $cm;
    }



}
