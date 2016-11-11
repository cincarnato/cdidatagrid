<?php

namespace CdiDataGrid\Factory;

use CdiDataGrid\Column;

/**
 * Description of ColumnFactory
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class ColumnFactory {

    /**
     * Column to create
     * 
     * @var \CdiDataGrid\Column\BaseColumn
     */
    protected $column = null;

    /**
     * Config
     * 
     * @var array
     */
    protected $config = array();

    public function create($name, Array $config) {
        $this->column = null;
        $this->config = $config;

        $type = (isset($config['type'])) ? $config['type'] : "string";


        switch ($type) {
            case "string":
                $this->createStringColumn($name);
                break;
            case "boolean":
                $this->createBooleanColumn($name);
                break;
            case "datetime":
                $this->createBooleanColumn($name);
                break;
            default:
                $this->createStringColumn($name);
                break;
        }

        return $this->column;
    }

    /**
     * Configure basic properties
     *
     * @param string $name name of the column
     * @return \CdiDataGrid\Column\StringColumn
     */
    protected function baseConfig() {

        if (isset($this->config["displayName"])) {
            $this->column->setDisplayName($this->config["displayName"]);
        }

        if (isset($this->config["hidden"])) {
            $this->column->setHidden($this->config["hidden"]);
        }

        if (isset($this->config["tdClass"])) {
            $this->column->setTdClass($this->config["tdClass"]);
        }
    }

    /**
     * Create a String Column
     *
     * @param string $name name of the column
     * @return \CdiDataGrid\Column\StringColumn
     */
    protected function createStringColumn($name) {
        $this->column = new Column\StringColumn($name);
        $this->baseConfig();
        return $this->column;
    }

    /**
     * Create a Boolean Column
     *
     * @param string $name name of the column
     * @return \CdiDataGrid\Column\BooleanColumn
     */
    protected function createBooleanColumn($name) {
        $this->column = new Column\BooleanColumn($name);
        $this->baseConfig();

        if (isset($this->config["valueWhenTrue"])) {
            $this->column->setValueWhenTrue($this->config["valueWhenTrue"]);
        }
        if (isset($this->config["valueWhenFalse"])) {
            $this->column->setValueWhenFalse($this->config["valueWhenFalse"]);
        }

        return $this->column;
    }

    /**
     * Create a DateTime Column
     *
     * @param string $name name of the column
     * @return \CdiDataGrid\Column\DateTiemColumn
     */
    protected function createDateTimeColumn($name) {
        $this->column = new Column\DateTiemColumn($name);
        $this->baseConfig();

        if (isset($this->config["dateTimeFormat"])) {
            $this->column->setDateTimeFormat($this->config["dateTimeFormat"]);
        }

        return $this->column;
    }

}
