<?php

/**
 * Description of RenderOptions
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class RenderOptions {

     /**
     * @var string
     */
    protected $formView;
     /**
     * @var string
     */
    protected $gridView;
     /**
     * @var string
     */
    protected $detailView;
     /**
     * @var string
     */
    protected $paginationView;
    

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
