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


----Simple usage----

$grid = $this->getServiceLocator()->get('cdiGrid');
$source = new \CdiDataGrid\DataGrid\Source\Doctrine($this->getEntityManager(), 'CdiCrm\Entity\Ticket');
$grid->setSource($source);
$grid->setRecordsPerPage(5);
$grid->setTemplate("ajax");
$grid->prepare();
$view = new ViewModel(array('grid' => $grid));
return $view;

----In View----

<?php echo $this->CdiGrid($this->grid); ?>


----Obs----
#The default template need jQuery and Bootstrap


----Features----



