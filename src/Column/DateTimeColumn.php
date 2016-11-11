<?php

namespace CdiDataGrid\Column;

/**
 * Description of Column
 *
 * @author cincarnato
 */
class DateTimeColumn extends BaseColumn {

        const type = "datetime";
    
    /**
     * Format of datetime
     * 
     * @var string
     */
    protected $dateTimeFormat = "Y-m-d H:i:s";

    function getDateTimeFormat() {
        return $this->dateTimeFormat;
    }

    function setDateTimeFormat($dateTimeFormat) {
        $this->dateTimeFormat = $dateTimeFormat;
    }




}

?>
