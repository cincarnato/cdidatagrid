<?php

/*
 * This file is part of the Cdi package.
 *
 * (c) Cristian Incarnato
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Cristian Incarnato
 * @license http://opensource.org/licenses/BSD-3-Clause
 * 
 */
return array(
    'router' => array(
        'routes' => array(
            'cdidatagrid' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/cdidatagrid/crud/:entity[/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'CdiDataGrid\Controller',
                        'controller' => 'CdiDataGrid\Controller\Grid',
                    ),
                    'constraints' => array(
                        'entity' => '[a-zA-Z\-0-9]+'
                    ),
                ),
            ),
        ),
    ),
    'CdiDatagrid' => array(),
    'controllers' => array(
        'invokables' => array(
            'CdiDataGrid\Controller\Grid' => 'CdiDataGrid\Controller\GridController',
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
        ),
        'factories' => array(
            'cdiGrid' => 'CdiDataGrid\Service\GridFactory',
            'doctrineAdapter' => 'CdiDataGrid\Service\AdapterDoctrineFactory',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'RenderBootstrapGrid' => 'CdiDataGrid\View\Helper\RenderBootstrapGrid',
              'ColumnBoolean' => 'CdiDataGrid\View\Helper\ColumnBoolean',
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
    ),
);
