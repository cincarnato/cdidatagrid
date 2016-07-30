cdidatagrid
==========


Modulo/Libreria para Zend Framework 2 que pretende resolver la presentacion en formato de tabla / grid, 
trabajando principalmente con entidades de Doctrine 2.


![alt tag](https://cloud.githubusercontent.com/assets/7002030/17273141/a97f1a0c-5681-11e6-90ee-de90f478c4af.jpg)


Caracteristicas / Funcionalidades / features:
- Paginador / Paginator
- Filtros / Filter
- Ordenar / Order
- Columnas Extras / Extra Column
- Render de campo personalizado con ViewHelper / Custom View Helper field
- Oculatar columnas / Hidden Column
- Tooltip


## Simple usage
```PHP
$grid = $this->getServiceLocator()->get('cdiGrid');
$source = new \CdiDataGrid\DataGrid\Source\Doctrine($this->getEntityManager(), 'CdiCrm\Entity\Ticket');
$grid->setSource($source);
$grid->setRecordsPerPage(5);
$grid->prepare();
$view = new ViewModel(array('grid' => $grid));
return $view;
```
## In View

```PHP
<?php echo $this->CdiGrid($this->grid); ?>
```

## Obs

-The default template need jQuery and Bootstrap


##  Features

### Hidden Column
```PHP
$grid->hiddenColumn('columnName');
```

### Change Column Visual Name
```PHP
$grid->changeColumnName('columnName','newColumnName');
```

### Set DateTime Column Format
```PHP
$grid->datetimeColumn('createdAt', 'Y-m-d H:i:s');
```

### Set render a column with a custom view helper
```PHP
$grid->customHelperColumn('columnname',"ViewHelperName");
```


### Set Template to Render
```PHP
$grid->setTemplate("ajax");
```

### Set a custom class to table
```PHP
$grid->setTableClass("customClass table-condensed table-striped");
```

### Set a custom class to Column (<td>)
```PHP
$grid->classTdColumn('columnName', "customClass text-center col-md-1");
```
