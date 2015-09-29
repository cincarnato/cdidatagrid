<?php

namespace CdiDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RenderGrid
 *
 * @author cincarnato
 */
class RenderCdiGrid extends AbstractHelper implements ServiceLocatorAwareInterface {

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


        $routeParams = $grid->getQueryArray();
        if (!$routeParams) {
            $routeParams = array();
        }
        $route = $grid->getRoute();
        $view = new ViewModel(array(
            'grid' => $grid,
            'gview' => $this->getView(),
            'routeParams' => $routeParams,
            'route' => $route
        ));

        $viewRender = new PhpRenderer();
        $resolver = new TemplateMapResolver();
        $resolver->setMap(array(
            'gridBootstrap' => __DIR__ . '/../Scripts/grid-bootstrap.phtml',
            'formBootstrap' => __DIR__ . '/../Scripts/form-bootstrap.phtml',
            'gridStandard' => __DIR__ . '/../Scripts/grid-standard.phtml',
            'formStandard' => __DIR__ . '/../Scripts/form-standard.phtml',
            'gridCustom' => __DIR__ . '/../Scripts/grid-custom.phtml',
            'formCustom' => __DIR__ . '/../Scripts/form-custom.phtml',
            'pagination' => __DIR__ . '/../Scripts/pagination.phtml'
        ));
        $viewRender->setResolver($resolver);




        switch ($grid->getInstanceToRender()) {
            case "formEntity":
                switch ($grid->getRenderTemplate()) {
                    case "standard":
                        $view->setTemplate('formStandard');

                        break;
                    case "bootstrap":
                        $view->setTemplate('formBootstrap');

                        break;
                    case "custom":
                        $view->setTemplate('formCustom');

                        break;

                    default:
                        $view->setTemplate('formBootstrap');
                        break;
                }

                break;
            case "grid":
                switch ($grid->getRenderTemplate()) {
                    case "standard":
                        $view->setTemplate('gridStandard');

                        break;
                    case "bootstrap":
                        $view->setTemplate('gridBootstrap');

                        break;
                    case "custom":
                        $view->setTemplate('gridCustom');

                        break;

                    default:
                        $view->setTemplate('gridBootstrap');
                        break;
                }

                break;

            default:
                switch ($grid->getRenderTemplate()) {
                    case "standard":
                        $view->setTemplate('gridStandard');

                        break;
                    case "bootstrap":
                        $view->setTemplate('gridBootstrap');

                        break;
                    case "custom":
                        $view->setTemplate('gridCustom');

                        break;

                    default:
                        $view->setTemplate('gridBootstrap');
                        break;
                }
                break;
        }







        return $viewRender->render($view);
    }

}

?>
