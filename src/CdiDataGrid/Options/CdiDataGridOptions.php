<?php

namespace CdiDataGrid\Options;

use Zend\Stdlib\AbstractOptions;

class CdiDataGridOptions extends AbstractOptions {

    
    
    protected $formView;
    protected $gridView;
    protected $detailView;
    protected $paginationView;

    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;
    
    /**
     * Name of partial for form view
     *
     * @return string
     */

    function getFormView() {
        return $this->formView;
    }

    function getGridView() {
        return $this->gridView;
    }

    function getDetailView() {
        return $this->detailView;
    }

    function setFormView($formView) {
        $this->formView = $formView;
    }

    function setGridView($gridView) {
        $this->gridView = $gridView;
    }

    function setDetailView($detailView) {
        $this->detailView = $detailView;
    }
    function getPaginationView() {
        return $this->paginationView;
    }

    function setPaginationView($paginationView) {
        $this->paginationView = $paginationView;
    }




}
