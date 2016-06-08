<?php

namespace CdiDataGrid\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CdiDataGrid\DataGrid\Grid as Grid;

class GridFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->get('config');

        if (!isset($config['CdiDatagrid'])) {
            throw new InvalidArgumentException('Config "CdiDatagrid" is missing');
        }

        /* @var $application \Zend\Mvc\Application */
        $application = $serviceLocator->get('application');

        $grid = new Grid();
        $grid->setOptions($config['CdiDatagrid']);
        $grid->setMvcEvent($application->getMvcEvent());
         $grid->setServiceLocator($serviceLocator);
        return $grid;
    }

}

?>
