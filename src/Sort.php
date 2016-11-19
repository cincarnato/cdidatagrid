<?php

namespace CdiDataGrid;

/**
 * Description of Order
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class Sort {

    /**
     * column
     * 
     * @var \CdiDataGrid\Column\ColumnInterface
     */
    protected $column;

    /**
     * By
     * 
     * @var string
     */
    protected $by;

    /**
     * Direction
     * 
     * @var string
     */
    protected $direction;

    function getBy() {
        return $this->by;
    }

    function getDirection() {
        return $this->direction;
    }

    function setBy($by) {
        $this->by = $by;
    }

    function setDirection($direction) {
        if ($direction != "DESC" && $direction != "ASC") {
            throw new \Exception("Order Direction only can be 'DESC' | 'ASC'");
        }
        $this->direction = $direction;
    }

    function getColumn() {
        return $this->column;
    }

    function setColumn(\CdiDataGrid\Column\ColumnInterface $column) {
        $this->column = $column;
    }


}
