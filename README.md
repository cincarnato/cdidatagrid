# cdidatagrid


Zend Framework 2 module.

It allows you to render a datagrid from an entity Doctrine 2.

Features: Pager, Filter, Order, Add, edit, view and delete records

-----------------------------------------------------------------------

Modulo para Zend Framework 2.

Permite renderizar un datagrid a partir de una entidad de Doctrine 2. 

Caracteristicas: Paginar,Filtrar, Ordenar, Agregar, editar, visualizar y eliminar registros


##Ejemplo/Example:

![alt tag](https://cloud.githubusercontent.com/assets/7002030/17273141/a97f1a0c-5681-11e6-90ee-de90f478c4af.jpg)



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


##  Functions

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

### Set a custom class to Column (td)
```PHP
$grid->classTdColumn('columnName', "customClass text-center col-md-1");
```


many more...
