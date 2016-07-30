<?php

namespace CdiDataGrid\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CdiDataGrid\DataGrid\Grid as Grid;

class GridFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->get('cdidatagrid_options');

        /* @var $application \Zend\Mvc\Application */
        $application = $serviceLocator->get('application');

        $grid = new Grid();
        $grid->setOptions($config);
        $grid->setMvcEvent($application->getMvcEvent());
        $grid->setServiceLocator($serviceLocator);
        return $grid;
    }

}

?>
