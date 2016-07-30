<?php

namespace CdiDataGrid\Options;

use Zend\Stdlib\AbstractOptions;

class GridOptions extends AbstractOptions implements GridOptionsInterface {

    /**
     * @var array
     */
    protected $templates;
    
     /**
     * @var integer
     */
    protected $recordsPerPage = 10;
    

    /**
     * @return array
     */
    public function getTemplates() {
        return $this->templates;
    }

    /**
     * @param array $templates
     * @return $this
     */
    public function setTemplates($templates) {
        $this->templates = (array) $templates;
        return $this;
    }
    
    function getRecordsPerPage() {
        return $this->recordsPerPage;
    }

    function setRecordsPerPage($recordsPerPage) {
        $this->recordsPerPage = $recordsPerPage;
    }





}
