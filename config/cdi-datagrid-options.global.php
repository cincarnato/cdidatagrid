<?php

//move to root "config/autoload/"
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
    )
);
