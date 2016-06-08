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
            'RenderCdiGrid' => 'CdiDataGrid\View\Helper\RenderCdiGrid',
            'RenderBootstrapGrid' => 'CdiDataGrid\View\Helper\RenderBootstrapGrid',
            'JsCrud' => 'CdiDataGrid\View\Helper\JsCrud',
            'JsAbmAjaxModal' => 'CdiDataGrid\View\Helper\JsAbmAjaxModal',
            'ColumnBoolean' => 'CdiDataGrid\View\Helper\ColumnBoolean',
            'Clink' => 'CdiDataGrid\View\Helper\Clink',
            'CdiGrid' => 'CdiDataGrid\View\Helper\Grid',
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'cdidatagrid' => __DIR__ . '/../view',
        ),
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'template_map' => array(
            'widget/csvForm' => __DIR__ . '/../view/widget/csv-form.phtml',
        ),
    ),
    'cdidatagrid_options' => array(
        'from_view' => 'cdidatagrid/form/form-bootstrap',
        'grid_view' => 'cdidatagrid/grid/grid-bootstrap',
        'detail_view' => 'cdidatagrid/detail/detail-bootstrap',
        'pagination_view' => 'cdidatagrid/pagination/pagination-bootstrap'
    ),
    'controller_plugins' => array(
		'invokables' => array(
			'CdiDatagridRefresh' => 'CdiDatagrid\Controller\Plugin\Refresh',

		),

	),
);
