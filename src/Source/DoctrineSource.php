<?php

namespace CdiDataGrid\Source;

use CdiDataGrid\Source\AbstractSource;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrinePaginatorAdapter;
use \Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class DoctrineSource extends AbstractSource implements SourceInterface {

    use \CdiDataGrid\Source\Doctrine\CrudTrait;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Datagrid Entity Name
     * 
     * @var string
     */
    protected $entityName;

    /**
     * Entity key
     * 
     * @var string
     */
    protected $entityKey = 'u';

    /**
     * Description
     * 
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $qb;

    /**
     * 
     * @var \CdiDataGrid\Filter\Filters
     */
    protected $filters;

    /**
     * Description
     * 
     * @var type
     */
    protected $paginator;
    //TOREVIEW

    protected $orderBy;
    protected $orderDirection;

    /**
     * Doctrine Source Construct
     *
     * @param \Doctrine\ORM\EntityManager $em 
     * @param string $entityName 
     * @param \Doctrine\ORM\QueryBuilder $qb 
     */
    function __construct(\Doctrine\ORM\EntityManager $em, $entityName, $qb = null) {
        $this->setEm($em);
        $this->setEntityName($entityName);
        if (isset($qb)) {
            $this->setQb($qb);
        }
    }

    public function getEm() {
        if (!isset($this->em)) {
            throw new \CdiDataGrid\Exception\EntityManagerNoSetException();
        }
        return $this->em;
    }

    public function setEm(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
        return $this;
    }

    public function getEntityName() {
        if (!isset($this->entityName)) {
            throw new \Exception("No EntityName set");
        }
        return $this->entityName;
    }

    public function setEntityName($entityName) {
        $this->entityName = $entityName;
        return $this;
    }

    protected function createQb() {
        $this->qb = $this->getEm()->createQueryBuilder()->select($this->getEntityKey())->from($this->getEntityName(), $this->getEntityKey());
    }

    protected function extractEntityFromQb() {
        if (isset($this->qb)) {
            $this->entityName = $this->qb->getRootEntities()[0];
            $this->entityKey = $this->qb->getRootAliases()[0];
            return true;
        }
        return false;
    }

    public function getQb() {
        if (!isset($this->qb)) {
            $this->createQb();
        }
        return $this->qb;
    }

    public function setQb(\Doctrine\ORM\QueryBuilder $qb) {
        $this->qb = $qb;
        if ($this->entityName != $this->getQb()->getRootEntities()[0]) {
            throw new \Exception("EntityName is diferent to RootEntity in QueryBuilder");
        }
    }

    function getEntityKey() {
        return $this->entityKey;
    }

    function setEntityKey($entityKey) {
        $this->entityKey = $entityKey;
    }

    public function execute() {

        //1-ApplyOrder
        $this->applyOrder();


        //2-ApplyFilters
        $this->applyFilters();

        //3-Paginator

        $this->paginator = new DoctrinePaginatorAdapter(new DoctrinePaginator($this->getQb()));

        return $this->paginator;
    }

    public function pullColumns() {
        $rp = $this->getEm()->getClassMetadata($this->entityName)->getReflectionProperties();
        return array_keys($rp);
    }

    public function applyFilters() {
        $doctrineFilter = new \CdiDataGrid\Source\Doctrine\Filter($this->getQb());
        if (is_a($this->getFilters(), "\CdiDataGrid\Filter\Filters")) {
            foreach ($this->getFilters() as $key => $filter) {
                $doctrineFilter->applyFilter($filter, $key);
            }
        }
    }

    public function applyOrder() {
        if ($this->orderBy && ($this->orderDirection == "DESC" || $this->orderDirection == "ASC")) {
            $ra = $this->qb->getRootAliases();
            $this->qb->orderBy($ra[0] . $this->orderBy, $this->orderDirection);
        }
    }

    function getFilters() {
        return $this->filters;
    }

    public function setFilters(\CdiDataGrid\Filter\Filters $filters) {
        $this->filters = $filters;
    }

    public function setOrder($orderBy, $direction) {
        $this->orderBy = $orderBy;
        $this->orderDirection = $direction;
    }

}
