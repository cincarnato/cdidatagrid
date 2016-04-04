<?php

namespace CdiDataGrid\DataGrid;

use \Zend\Mvc\MvcEvent as MvcEvent;
use \Zend\Paginator\Paginator;
use Zend\Form\Form;
use Zend\Form\Element;
use CdiDataGrid\DataGrid\Column\Column;
use CdiDataGrid\DataGrid\Column\ExtraColumn;
use Zend\Http\Response\Stream as ResponseStream;
use Zend\Http\Headers;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Grid
 *
 * @author cincarnato
 */
class Grid {

    //put your code here

    protected $id;
    protected $title;
    protected $source;
    protected $page;
    protected $paginator;
    protected $paginatorAdapter;
    protected $unprocessedData;
    protected $row;
    protected $filters;
    protected $orderBy;
    protected $columnsName = array();
    protected $columnCollection = array();
    protected $OrderColumnCollection = array();
    protected $extraColumnCollection = array();
    protected $selectFilterCollection = array();
    protected $renameColumnCollection = array();
    protected $hiddenColumnCollection = array();
    protected $tooltipColumnCollection = array();
    protected $linkColumnCollection = array();
    protected $longTextColumnCollection = array();
    protected $booleanColumnCollection = array();
    protected $datetimeColumnCollection = array();
    protected $aditionalHtmlColumnCollection = array();
    protected $recordPerPage = 10;
    protected $renderOk = false;
    protected $options = "";
    protected $mvcEvent;
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
    protected $addBtn = null;
    protected $instanceToRender = "grid";
    protected $renderTemplate = "bootstrap";
    protected $columnFilter = true;
    protected $columnOrder = true;

    CONST DEFAULT_RENDER = "shtml";

    public function __construct() {
        
    }

    public function prepare() {

        $returnCrud = $this->verifyCrudActions();

        $this->extractColumns();
        $this->prepareFilters();
        $this->prepareOrder();
        $this->getSource()->setFilters($this->filters);
        $this->getSource()->setOrder($this->orderBy, $this->orderDirection);
        $this->paginatorAdapter = $this->getSource()->query();

        $this->paginator = new Paginator($this->paginatorAdapter);
        $this->paginator->setDefaultItemCountPerPage($this->getRecordPerPage());
        $this->paginator->setCurrentPageNumber($this->getPage());

        $this->unprocessedData = $this->paginator->getCurrentItems();

        $this->processData();
        $this->mergeExtraColumn();
        $this->processFormFilters();
        $this->processRenameColumns();
        $this->processHiddenColumns();
        $this->processTooltipColumns();
        $this->processBooleanColumns();
        $this->processDatetimeColumns();
        $this->processLinkColumns();
        $this->processLongTextColumns();
        $this->processAditionalHtmlColumns();

        $this->processOrderColumn();

        $export = $this->exportCsv();
        return $this->export;
    }

    protected function verifyCrudActions() {
        $aData = $this->getPost();

        if (isset($aData["crudAction"])) {

            if ($aData["crudAction"] == 'delete') {

                $return = $this->getSource()->delRecord($aData["crudId"]);
                echo "Delete:" . $aData["crudId"];
                return $return;
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

    public function getEntityForm() {
        return $this->getSource()->getEntityForm();
    }

    public function generateEntityForm() {



        return $this->formEntity;
    }

    public function addDelOption($name, $side, $btnClass, $btnVal = null) {
        $this->setOptionDelete(true);
        $originalValue = "<i class='" . $btnClass . "' onclick='deleteRecord({{id}})'>" . $btnVal . "</i>";
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
        $originalValue = "<i class='" . $btnClass . "' onclick='editRecord({{id}})'>" . $btnVal . "</i>";
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
        $this->addBtn = "<i id='.$name.' name='.$name.' class='" . $btnClass . "' onclick='addRecord()'>" . $btnVal . "</i>";
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
        return $this->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
    }

    public function getQuery() {
        return $this->getMvcEvent()->getRequest()->getQuery();
    }

    public function getPost() {
        return $this->getMvcEvent()->getRequest()->getPost();
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

                            if (key_exists($key, $this->selectFilterCollection)) {

                                $match = true;
                                $type = "eq";
                            }

                            if (preg_match("/==/", $value)) {
                                $match = true;
                                $type = "eq";
                                $value = str_replace("==", "", $value);
                            }

                            if (preg_match("/$</", $value)) {
                                $match = true;
                                $type = "lt";
                                $value = str_replace("<", "", $value);
                            }

                            if (preg_match("/$>/", $value)) {
                                $match = true;
                                $type = "gt";
                                $value = str_replace(">", "", $value);
                            }

                            if (preg_match("/<>/", $value)) {
                                $match = true;
                                $type = "between";
                                $values = explode("<>", $value);
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

    public function setFormFilterSelect($ColumnName, \Zend\Form\Element\Select $filter) {
        $name = "f_" . $ColumnName;
        $filter->setName($name);

        $this->selectFilterCollection[$name] = $filter;
    }

    protected function processFormFilters() {

        $this->formFilters = new \Zend\Form\Form();
        $this->formFilters->setName("GridFormFilters");
        //$this->formFilters->setOption("action", "");
        $this->formFilters->setAttribute('method', 'get');

        foreach ($this->columnCollection as $column) {
            if ($column->getFilterActive()) {

                $name = "f_" . $column->getName();
                if (key_exists($name, $this->selectFilterCollection)) {

                    $this->formFilters->add($this->selectFilterCollection[$name]);
                } else {
                    $element = new Element\Text($name);
                    $this->formFilters->add($element);
                }
            }
        }

        $this->formFilters->add(array(
            'name' => 'submit',
            'options' => array('label' => 'Aplicar Filtros'),
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Aplicar Filtros'
            )
        ));

        $this->formFilters->add(array(
            'name' => 'page',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPage()
            )
        ));


        $request = $this->getMvcEvent()->getRequest()->getQuery();

        $this->formFilters->setData($request);
    }

    public function hiddenColumn($columnName) {
        $this->hiddenColumnCollection[$columnName] = $columnName;
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

    protected function processData() {

        foreach ($this->unprocessedData as $record) {
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
            array_push($this->columnCollection, $column);
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
            if (key_exists($column->getName(), $this->renameColumnCollection))
                $column->setVisualName($this->renameColumnCollection[$column->getName()]);
        }
    }

    protected function processHiddenColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->hiddenColumnCollection))
                $column->setHidden(true);
        }
    }

    protected function processDatetimeColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->datetimeColumnCollection))
                $column->setType("datetime");
            $column->setFormatDatetime($this->datetimeColumnCollection[$column->getName()]);
        }
    }

    protected function processLinkColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->linkColumnCollection))
                $column->setType("link");
        }
    }

    protected function processLongTextColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->longTextColumnCollection))
                $column->setType("longText");
            $column->setLength($this->longTextColumnCollection[$column->getName()]);
        }
    }

    protected function processTooltipColumns() {
        foreach ($this->columnCollection as &$column) {
            if (key_exists($column->getName(), $this->tooltipColumnCollection))
                $column->setTooltip($this->tooltipColumnCollection[$column->getName()]);
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

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getRenderOk() {
        return $this->renderOk;
    }

    public function getOptions() {
        return $this->options;
    }

    public function setOptions($options) {
        $this->options = $options;
    }

    public function getMvcEvent() {
        return $this->mvcEvent;
    }

    public function setMvcEvent(MvcEvent $mvcEvent) {
        $this->mvcEvent = $mvcEvent;
    }

    public function getSource() {
        return $this->source;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function getPage() {
        if (!$this->page) {
            $page = $this->getMvcEvent()->getRequest()->getQuery('page');
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

    public function getPaginator() {
        return $this->paginator;
    }

    public function getRecordPerPage() {
        return $this->recordPerPage;
    }

    public function setRecordPerPage($recordPerPage) {
        $this->recordPerPage = $recordPerPage;
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



}
