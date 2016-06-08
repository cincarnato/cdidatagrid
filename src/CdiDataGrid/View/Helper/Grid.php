<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Grid extends AbstractHelper implements ServiceLocatorAwareInterface {

    /**
     * Set the service locator. 
     * 
     * @param ServiceLocatorInterface $serviceLocator 
     * @return CustomHelper 
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get the service locator. 
     * 
     * @return \Zend\ServiceManager\ServiceLocatorInterface 
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function __invoke(\CdiDataGrid\DataGrid\Grid $grid) {

        /* @var $cdiDataGridOptions \CdiDataGrid\Options\CdiDataGridOptions   */
        $cdiDataGridOptions = $this->getServiceLocator()->getServiceLocator()->get('cdidatagrid_options');



        switch ($grid->getInstanceToRender()) {
            case "formEntity":
                if ($cdiDataGridOptions->getFormView() != null) {
                    $partial = $cdiDataGridOptions->getFormView();
                } else {
                    $partial = "cdidatagrid/form/form-bootstrap";
                }

                break;
            case "grid":
                if ($cdiDataGridOptions->getGridView() != null) {
                    $partial = $cdiDataGridOptions->getGridView();
                } else {
                    $partial = "cdidatagrid/grid/grid-bootstrap";
                }

                if ($cdiDataGridOptions->getPaginationView() != null) {
                    $partialPagination = $cdiDataGridOptions->getPaginationView();
                } else {
                    $partialPagination = "cdidatagrid/pagination/pagination-bootstrap";
                }

                break;
            case "detail":
                if ($cdiDataGridOptions->getDetailView() != null) {
                    $partial = $cdiDataGridOptions->getDetailView();
                } else {
                    $partial = "cdidatagrid/detail/detail-bootstrap";
                }
                break;
            default:
                $partial = "cdidatagrid/table/grid-bootstrap";
                break;
        }

        $routeParams = $grid->getQueryArray();
        if (!$routeParams) {
            $routeParams = array();
        }
        $route = $grid->getRoute();

        //ver en partial poner el pagination como parametro opcional

        return $this->view->partial($partial, array(
                    "grid" => $grid,
                    "partialPagination" => $partialPagination,
                    'routeParams' => $routeParams,
                    'route' => $route));
    }

}
