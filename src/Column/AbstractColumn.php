<?php

namespace CdiDataGrid\Column;

/**
 * Description of AbstractColumn
 *
 * @author cincarnato
 */
abstract class AbstractColumn implements ColumnInterface {

    const type = "abstract";

    /**
     * Name of the column
     * 
     * @var string
     */
    protected $name;

    /**
     * Show or hidde the column
     * 
     * @var boolean
     */
    protected $hidden = false;

    /**
     * Display Name to show
     * 
     * @var string
     */
    protected $displayName;

    /**
     * Valid type of columns
     * 
     * @var array
     */
    protected $validTypes = array(
        "string" => true,
        "text" => true,
        "integer" => true,
        "decimal" => true,
        "date" => true,
        "time" => true,
        "datetime" => true
    );

    function __construct($name) {
        $this->setName($name);
        $this->setDisplayName($name);
    }

    public function getName() {
        return $this->name;
    }

    function setName($name) {
        $this->name = $name;
    }

    function getType() {
        return static::type;
    }

    function getHidden() {
        return $this->hidden;
    }

    function setHidden($hidden) {
        $this->hidden = $hidden;
    }

    function getDisplayName() {
        return $this->displayName;
    }

    function setDisplayName($displayName) {
        $this->displayName = $displayName;
    }

    public function __toString() {
        return $this->displayName;
    }

}

?>
