<?php
namespace CdiDataGrid\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CdiDataGrid\Grid;

class GridFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL)
    {
        /* @var $config \CdiDataGrid\Opcions\GridOptions */
        $config = $container->get('cdidatagrid_options');
        
        /* @var $application \Zend\Mvc\Application */
        $application = $container->get('application');
        
        /* @var $mvcevent \Zend\Mvc\MvcEvent */
        $mvcevent = $application->getMvcEvent();
        
        $grid = new Grid($mvcevent);
        $grid->setOptions($config);

        return $grid;
    }

}
