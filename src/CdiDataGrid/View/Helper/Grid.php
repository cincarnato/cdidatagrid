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

        $template = $grid->getTemplate();
        $templates = $grid->getOptions()->getTemplates();

        switch ($grid->getInstanceToRender()) {
            case "formEntity":
                $partial = $templates[$template]["form_view"];
                break;
            case "grid":
                $partial = $templates[$template]["grid_view"];
                break;
            case "detail":
                $partial = $templates[$template]["detail_view"];
                break;
            default:
                $partial = $templates[$template]["grid_view"];
                break;
        }

        $partialPagination = $templates[$template]["pagination_view"];

        $routeParams = $grid->getQueryArray();
        if (!$routeParams) {
            $routeParams = array();
        }
        $route = $grid->getRoute();

        return $this->view->partial($partial, array(
                    "grid" => $grid,
                    "partialPagination" => $partialPagination,
                    'routeParams' => $routeParams,
                    'route' => $route));
    }

}
