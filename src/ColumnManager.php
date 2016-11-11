<?php

namespace CdiDataGrid;

use CdiDataGrid\DataGrid\Column\Column;
use CdiDataGrid\DataGrid\Column\ExtraColumn;
/**
 * Class to handle grid columns
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class ColumnManager {
    
    
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

}
