<?php

namespace CdiDataGrid\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CdiDataGrid\Grid;

class GridFactory implements FactoryInterface {

    protected $container;
    protected $grid;
    protected $gridOptions;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $this->container = $container;

        /* @var $config \CdiDataGrid\Opcions\GridOptions */
        $gridOptions = $container->get('cdidatagrid_options');

        /* @var $application \Zend\Mvc\Application */
        $application = $container->get('application');

        /* @var $mvcevent \Zend\Mvc\MvcEvent */
        $mvcevent = $application->getMvcEvent();

        //CUSTOM OPTIONS
        if (isset($options["customOptionsKey"])) {
            $gridOptions->mergeCustomOptionsByKey($options["customOptionsKey"]);
        }

        $this->gridOptions = $gridOptions;

        //NEW GRID
        $this->grid = new Grid($mvcevent, $gridOptions);

        //SET SOURCE BY REQUEST NAME
        ($requestedName == "CdiDatagridDoctrine") ? $this->buildDoctrineSource() : null;

        return $this->grid;
    }

    protected function buildDoctrineSource() {
        $sourceConfig = $this->gridOptions->getSourceConfig();
        if (isset($sourceConfig["entityManager"])) {
            $em = $this->container->get($sourceConfig["entityManager"]);
        } else {
            $em = $this->container->get('Doctrine\ORM\EntityManager');
        }

        if (isset($sourceConfig["queryBuilder"])) {
            $qb = $sourceConfig["queryBuilder"];
        } else {
            $qb = $em->createQueryBuilder('u');
        }

        $source = new \CdiDataGrid\Source\DoctrineSource($qb);
        $source->setEm($em);

        if (isset($sourceConfig["entityName"])) {
            $qb->select('u')->from($sourceConfig["entityName"], 'u');
            $source->setEntityName($sourceConfig["entityName"]);
        }

        $this->grid->setSource($source);
    }

}
