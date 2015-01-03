<?php

namespace CdiDataGrid\DataGrid\Source;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use \Doctrine\ORM\Tools\Pagination\Paginator;

class Doctrine extends AbstractSource {

    protected $entityManager;
    protected $entity;
    protected $repository;
    protected $query;
    protected $filters;
    protected $orderBy;
    protected $orderDirection;

    public function __construct($entityManager, $entity, $query = null) {
        $this->setEntityManager($entityManager);
        $this->setEntity($entity);
        $this->setRepository($this->getEntityManager()->getRepository($this->getEntity()));
        if ($query) {
            $this->setQuery($query);
        }
    }

    public function query() {
        $this->queryBuldier();
        $this->queryFilters();
        $this->queryOrder();

        $paginatorAdapter = new DoctrinePaginator(new Paginator($this->getQuery()));

        return $paginatorAdapter;
    }

    public function queryBuldier() {
        if (!$this->query) {
            $query = $this->getEntityManager()->createQueryBuilder('u');
            $query->select('u')->from($this->getEntity(), 'u');

            $this->setQuery($query);
            return $this->query;
        } else {
            return $this->query;
        }
    }

    public function getColumns() {
        //$fieldNames = $this->getEntityManager()->getClassMetadata($this->entity)->getFieldNames();
        $rp = $this->getEntityManager()->getClassMetadata($this->entity)->getReflectionProperties();
        foreach ($rp as $key => $value) {
            $fieldNames[] = $key;
        }
        return $fieldNames;
    }

    public function queryFilters() {
        $where = "";
        foreach ($this->filters as $filter) {

            if ($filter["type"] == "like") {
                $value = "%" . $filter['value'] . "%";
                $this->query->andWhere($this->query->expr()->like("u." . $filter["key"], $this->query->expr()->literal($value)));
            }
            if ($filter["type"] == "eq") {
                $value = $filter['value'];
                $this->query->andWhere($this->query->expr()->eq("u." . $filter["key"], $this->query->expr()->literal($value)));
            }
        }
    }

    public function queryOrder() {
        if($this->orderBy && ($this->orderDirection == "DESC" || $this->orderDirection == "ASC") ){
             $this->query->orderBy("u.$this->orderBy", $this->orderDirection);
        }
    }

    public function getEntityManager() {
        return $this->entityManager;
    }

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function getEntity() {
        return $this->entity;
    }

    public function setEntity($entity) {
        $this->entity = $entity;
    }

    public function getQuery() {
        if (!$this->query) {
            $this->queryBuldier();
        }
        return $this->query;
    }

    public function setQuery($query) {
        $this->query = $query;
    }

    public function getRepository() {
        return $this->repository;
    }

    public function setRepository($repository) {
        $this->repository = $repository;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setFilters(array $filters) {
        $this->filters = $filters;
    }

    public function setOrder($orderBy, $direction) {
        $this->orderBy = $orderBy;
          $this->orderDirection = $direction;
    }

}
?>

