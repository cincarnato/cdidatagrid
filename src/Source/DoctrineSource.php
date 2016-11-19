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
     * @var \CdiDataGrid\Filters
     */
    protected $filters;

    /**
     * Description
     * 
     * @var type
     */
    protected $paginator;
    //TOREVIEW

    /**
     * Order
     * 
     * @var \CdiDataGrid\Sort
     */
    protected $sort;

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


        //1-ApplyFilters
        $this->applyFilters();

        //2-ApplyOrder
        $this->applySort();
        
        
        //echo $this->getQb()->getDQL();

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
        if (is_a($this->getFilters(), "\CdiDataGrid\Filters")) {
            foreach ($this->getFilters() as $key => $filter) {
                $doctrineFilter->applyFilter($filter, $key);
            }
        }
    }

    public function applySort() {
        if (isset($this->sort) && $this->sort instanceof \CdiDataGrid\Sort) {
            $ra = $this->getQb()->getRootAliases();
            $ro = $ra[0] . ".";
            
            if($this->sort->getColumn()->getType() == 'relational'){
                $this->getQb()->leftJoin($ro.$this->sort->getColumn()->getName(), 't');
                $this->getQb()->orderBy('t.'.$this->sort->getColumn()->getOrderProperty() , $this->sort->getDirection());
            }else{
                $this->getQb()->orderBy($ro . $this->sort->getBy(), $this->sort->getDirection());
            }
            
            
            
        }
    }

    function getFilters() {
        return $this->filters;
    }

    public function setFilters(\CdiDataGrid\Filters $filters) {
        $this->filters = $filters;
    }

    function getSort() {
        return $this->sort;
    }

    function setSort(\CdiDataGrid\Sort $sort) {
        $this->sort = $sort;
    }

}
