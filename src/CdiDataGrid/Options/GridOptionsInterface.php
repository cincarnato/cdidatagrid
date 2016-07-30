<?php

namespace CdiDataGrid\Options;

/**
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
interface GridOptionsInterface {

    function getTemplates();

    function setTemplates($template);

    function getRecordsPerPage();

    function setRecordsPerPage($recordPerPage);
}
