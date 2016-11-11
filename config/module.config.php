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
    'cdidatagrid_options' => array(
        'recordsPerPage' => 10,
        'templates' => array(
            'default' => array(
                'form_view' => 'cdidatagrid/form/form-default',
                'grid_view' => 'cdidatagrid/grid/grid-default',
                'detail_view' => 'cdidatagrid/detail/detail-default',
                'pagination_view' => 'cdidatagrid/pagination/pagination-default'
            ),
            'ajax' => array(
                'form_view' => 'cdidatagrid/form/form-ajax',
                'grid_view' => 'cdidatagrid/grid/grid-ajax',
                'detail_view' => 'cdidatagrid/detail/detail-ajax',
                'pagination_view' => 'cdidatagrid/pagination/pagination-ajax'
            )
        )
    ),
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
    'view_helpers' => array(
        'invokables' => array(
            'RenderCdiGrid' => 'CdiDataGrid\View\Helper\Grid',
            'RenderBootstrapGrid' => 'CdiDataGrid\View\Helper\RenderBootstrapGrid',
            'JsCrud' => 'CdiDataGrid\View\Helper\JsCrud',
            'JsAbmAjaxModal' => 'CdiDataGrid\View\Helper\JsAbmAjaxModal',
            'ColumnBoolean' => 'CdiDataGrid\View\Helper\ColumnBoolean',
            'Clink' => 'CdiDataGrid\View\Helper\Clink',
            //News
            'CdiGrid' => 'CdiDataGrid\View\Helper\Grid',
            'CdiGridCrud' => 'CdiDataGrid\View\Helper\CdiGridCrud',
            'CdiGridCrudAjax' => 'CdiDataGrid\View\Helper\CdiGridCrudAjax',
            'CdiGridField' => 'CdiDataGrid\View\Helper\CdiGridField',
            'CdiGridFieldText' => 'CdiDataGrid\View\Helper\CdiGridFieldText',
            'CdiGridFieldBoolean' => 'CdiDataGrid\View\Helper\CdiGridFieldBoolean',
            'CdiGridFieldDateTime' => 'CdiDataGrid\View\Helper\CdiGridFieldDateTime',
            'CdiGridFieldExtra' => 'CdiDataGrid\View\Helper\CdiGridFieldExtra',
            'CdiGridFieldLink' => 'CdiDataGrid\View\Helper\CdiGridFieldLink',
            'CdiGridFieldLongText' => 'CdiDataGrid\View\Helper\CdiGridFieldLongText',
            'CdiGridFieldCustom' => 'CdiDataGrid\View\Helper\CdiGridFieldCustom',
            'CdiGridBtnAdd' => 'CdiDataGrid\View\Helper\CdiGridBtnAdd',
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
    'controller_plugins' => array(
        'invokables' => array(
            'CdiDatagridRefresh' => 'CdiDatagrid\Controller\Plugin\Refresh',
        ),
    ),
);
