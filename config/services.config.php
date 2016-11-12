<?php

/**
 * User: Cristian Incarnato
 */
use Zend\ServiceManager\ServiceLocatorInterface;

return array(
    'factories' => array(
        \CdiDataGrid\Grid::class => CdiDataGrid\Factory\GridFactory::class,
        \CdiDataGrid\Source\Doctrine\DoctrineSource::class => function(){
        return  CdiDataGrid\Source\Doctrine\DoctrineSource();
        },
        'cdidatagrid_options' => function (ServiceLocatorInterface $sm) {
            $config = $sm->get('Config');
            return new \CdiDataGrid\Options\GridOptions(isset($config['cdidatagrid_options']) ? $config['cdidatagrid_options'] : array());
        }
            )
        );
        
