<?php
namespace CdiDataGrid\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CdiDataGrid\Grid;

class GridFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        /* @var $config \CdiDataGrid\Opcions\GridOptions */
        $config = $container->get('cdidatagrid_options');
        
        /* @var $application \Zend\Mvc\Application */
        $application = $container->get('application');
        
        /* @var $mvcevent \Zend\Mvc\MvcEvent */
        $mvcevent = $application->getMvcEvent();
        
        /* @var $request \Zend\Http\Request */
        $request = $mvcevent->getRequest();
        
        /* @var $routematch \Zend\Router\RouteMatch */
        $routematch = $mvcevent->getRouteMatch();
        
        $grid = new Grid($request,$routematch);
        $grid->setOptions($config);

        return $grid;
    }

}
