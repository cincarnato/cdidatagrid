<?php
namespace CdiDataGrid\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CdiDataGrid\DataGrid\Adapter\DoctrineAdapter as Adapter;


class AdapterDoctrineFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $Adapter = new Adapter();
        return $Adapter;
    }
}
?>
