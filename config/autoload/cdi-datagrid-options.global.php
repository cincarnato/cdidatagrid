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
        ),
        "crudConfig" => [
            "enable" => true,
            "add" => [
                "enable" => true,
                "class" => " fa fa-plus cursor-pointer",
                "value" => " Agregar"
            ],
            "edit" => [
                "enable" => true,
                "class" => "fa fa-edit fa-xs cursor-pointer",
                "value" => ""
            ],
            "del" => [
                "enable" => true,
                "class" => "fa fa-trash cursor-pointer",
                "value" => ""
            ],
            "view" => [
                "enable" => true,
                "class" => " fa fa-list cursor-pointer",
                "value" => ""
            ]
        ],
    )
);
