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
class RenderBootstrapGrid extends AbstractHelper implements ServiceLocatorAwareInterface {

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
            'renderbootstrap' => __DIR__ . '/../Scripts/renderbootstrap.phtml',
            'pagination' => __DIR__ . '/../Scripts/pagination.phtml'
        ));
        $viewRender->setResolver($resolver);


        // $serviceManager = $this->getServiceLocator()->getServiceLocator();  
        // $pluginManager        = $serviceManager->get('ControllerPluginManager');
        // $viewRender->setHelperPluginManager($pluginManager);
        //  $router = $grid->getMvcEvent()->getRouter();

        $view->setTemplate('renderbootstrap');

        return $viewRender->render($view);
    }

}

?>
