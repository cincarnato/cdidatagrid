<?php

namespace CdiDataGrid\Column;

/**
 * Description of Column
 *
 * @author cincarnato
 */
class CrudColumn extends ExtraColumn {

    protected $side;
    protected $add = ["enable" => true, "class" => "btn btn-primary fa fa-plus", "value" => "", "action"=> "onclick='cdiAddRecord()'"];
    protected $edit = ["enable" => true, "class" => "btn btn-primary fa fa-edit", "value" => "","action"=> "onclick='cdiEditRecord({{id}})'"];
    protected $del = ["enable" => true, "class" => "btn btn-danger fa fa-trash", "value" => "","action"=> "onclick='cdiDeleteRecord({{id}})'"];
    protected $view = ["enable" => true, "class" => "btn btn-success fa fa-list", "value" => "","action"=> "onclick='cdiViewRecord({{id}})'"];
    protected $filterActive = true;
    protected $filter;

    function __construct($name, $side, $crudConfig) {
        $this->name = $name;
        $this->displayName = $name;
        $this->type = "crud";
        $this->setSide($side);

        (isset($crudConfig["add"])) ? $this->add = array_merge($this->add, $crudConfig["add"]) : null;
        (isset($crudConfig["edit"])) ? $this->edit = array_merge($this->edit, $crudConfig["edit"]) : null;
        (isset($crudConfig["del"])) ? $this->del = array_merge($this->del, $crudConfig["del"]) : null;
        (isset($crudConfig["view"])) ? $this->view = array_merge($this->view, $crudConfig["view"]) : null;

        
        if ($this->add["enable"]) {
            $this->displayName = " <i class='" . $this->add["class"] . "' ". $this->add["action"]." >" . $this->add["value"] . "</i>";
        }
        
        if ($this->edit["enable"]) {
            $this->originalValue = " <i class='" . $this->edit["class"] . "' ". $this->edit["action"]." >" . $this->edit["value"] . "</i>";
        }
        if ($this->del["enable"]) {
            $this->originalValue .= " <i class='" . $this->del["class"] . "' ". $this->del["action"]." >" . $this->del["value"] . "</i>";
        }
        if ($this->view["enable"]) {
            $this->originalValue .= " <i class='" . $this->view["class"] . "' ". $this->view["action"]." >" . $this->view["value"] . "</i>";
        }
    }

    public function __toString() {
        return $this->displayName;
    }

    public function getSide() {
        return $this->side;
    }

    public function setSide($side) {
        if ($side == "left" || $side == "right") {
            $this->side = $side;
        } else {
            throw new Exception("The side must be 'left' or 'right'");
        }
    }

    public function getFilterActive() {
        return $this->filterActive;
    }

    public function setFilterActive($filterActive) {
        $this->filterActive = $filterActive;
    }

    public function getFilter() {
        return $this->filter;
    }

    public function setFilter($filter) {
        $this->filter = $filter;
    }

    public function getOriginalValue() {
        return $this->originalValue;
    }

    public function setOriginalValue($originalValue) {
        $this->originalValue = $originalValue;
    }

    function getEdit() {
        return $this->edit;
    }

    function getDel() {
        return $this->del;
    }

    function getView() {
        return $this->view;
    }

    function setEdit($edit) {
        $this->edit = $edit;
    }

    function setDel($del) {
        $this->del = $del;
    }

    function setView($view) {
        $this->view = $view;
    }

    function getAdd() {
        return $this->add;
    }

    function setAdd($add) {
        $this->add = $add;
    }


    
}

?>
