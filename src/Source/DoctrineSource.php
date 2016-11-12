<?php

namespace CdiDataGrid\Source;

use CdiDataGrid\Source\AbstractSource;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrinePaginatorAdapter;
use \Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class DoctrineSource extends AbstractSource implements SourceInterface {

    use \CdiDataGrid\Source\Doctrine\CrudTrait;

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
     * Form to add or edit
     * 
     * @var type
     */
    protected $form;

    /**
     * Description
     * 
     * @var type
     */
    protected $paginator;
    //TOREVIEW

    protected $orderBy;
    protected $orderDirection;

    public function __construct($qb) {
        $this->setQb($qb);
    }

    public function execute() {

        //1-ApplyOrder
        $this->applyOrder();


        //2-ApplyFilters
        $this->applyFilters();

        //3-Paginator
        
        //echo $this->qb->getDQL();
        //var_dump($this->qb->getParameters());
        
        $this->paginator = new DoctrinePaginatorAdapter(new DoctrinePaginator($this->getQb()));

        return $this->paginator;
    }

    public function pullColumns() {
        $rp = $this->getEm()->getClassMetadata($this->entityName)->getReflectionProperties();
        return array_keys($rp);
    }

    function getQb() {
        return $this->qb;
    }

    function setQb(\Doctrine\ORM\QueryBuilder $qb) {
        $this->qb = $qb;
    }

    public function applyFilters() {
        $doctrineFilter = new \CdiDataGrid\Source\Doctrine\Filter($this->qb);
        if (is_a($this->getFilters(), "\CdiDataGrid\Filter\Filters")) {
            foreach ($this->getFilters() as $key => $filter) {
                $doctrineFilter->applyFilter($filter,$key);
            }
        }
    }

    public function applyOrder() {
        if ($this->orderBy && ($this->orderDirection == "DESC" || $this->orderDirection == "ASC")) {
            $ra = $this->qb->getRootAliases();
            $this->qb->orderBy($ra[0] . $this->orderBy, $this->orderDirection);
        }
    }

    function getForm() {
        if (!isset($this->form)) {
            $this->buildForm();
        }
        return $this->form;
    }

    function setForm($form) {
        $this->form = $form;
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
