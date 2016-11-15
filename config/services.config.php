<?php

/**
 * User: Cristian Incarnato
 */
use Zend\ServiceManager\ServiceLocatorInterface;

$services = [
    'factories' => [
        "CdiDatagrid" => CdiDataGrid\Factory\GridFactory::class,
        "CdiDatagridDoctrine" => CdiDataGrid\Factory\GridFactory::class,
        'cdidatagrid_options' => function (ServiceLocatorInterface $sm) {
            $config = $sm->get('Config');
            return new \CdiDataGrid\Options\GridOptions(isset($config['cdidatagrid_options']) ? $config['cdidatagrid_options'] : array());
        },
            ],
            'aliases' => [
                \CdiDataGrid\Grid::class => "CdiDatagrid"
            ]
        ];


        return $services;




        
