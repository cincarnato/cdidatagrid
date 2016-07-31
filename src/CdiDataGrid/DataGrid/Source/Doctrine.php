<?php

namespace CdiDataGrid\DataGrid\Source;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use \Doctrine\ORM\Tools\Pagination\Paginator;
use \Zend\Form\Annotation\AnnotationBuilder;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;

class Doctrine extends AbstractSource {

    protected $entityManager;
    protected $entity;
    protected $repository;
    protected $query;
    protected $filters;
    protected $orderBy;
    protected $orderDirection;
    public $entityForm;

    public function __construct($entityManager, $entity, $query = null) {
        $this->setEntityManager($entityManager);
        $this->setEntity($entity);
        $this->setRepository($this->getEntityManager()->getRepository($this->getEntity()));
        if ($query) {
            $this->setQuery($query);
        }
    }

    public function delRecord($id) {
        $record = $this->getEntityManager()->getRepository($this->entity)->find($id);
        $this->getEntityManager()->remove($record);
        $this->getEntityManager()->flush();
    }

    public function viewRecord($id) {
        $record = $this->getEntityManager()->getRepository($this->entity)->find($id);
        return $record;
    }

    public function updateRecord($id, $aData) {
        $this->generateEntityForm($id);

        $this->entityForm->setData($aData);

        if ($this->entityForm->isValid()) {
            $record = $this->entityForm->getObject();
            //Aqui deberia crear un evento en forma de escucha
            $argv = array('record' => $record, 'form' => $this->entityForm, 'data' => $aData);
            $this->getEventManager()->trigger(__FUNCTION__ . '_before', $this, $argv);
            $this->getEntityManager()->persist($record);
            $this->getEntityManager()->flush();
            $this->getEventManager()->trigger(__FUNCTION__ . '_post', $this, $argv);
            return true;
        } else {
            return false;
        }
    }

    public function saveRecord($aData) {
        $this->generateEntityForm();

        $this->entityForm->setData($aData);

        if ($this->entityForm->isValid()) {
            $record = $this->entityForm->getObject();
            $argv = array('record' => $record, 'form' => $this->entityForm, 'data' => $aData);
            $this->getEventManager()->trigger(__FUNCTION__ . '_before', $this, $argv);
            $this->getEntityManager()->persist($record);
            $this->getEntityManager()->flush();
            $argv["record"] = $record;
            $this->getEventManager()->trigger(__FUNCTION__ . '_post', $this, $argv);
            return true;
        } else {
            return false;
        }
    }

    public function getAllData($limit) {
        if ($limit) {
            return $this->getEntityManager()->createQuery($this->getQuery())
                            ->setMaxResults($limit)->getArrayResult();
        } else {
            return $this->getEntityManager()->createQuery($this->getQuery())->getArrayResult();
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

    public function getFieldMappings() {
        $fieldMappings = $this->getEntityManager()->getClassMetadata($this->entity)->fieldMappings;
        return $fieldMappings;
    }

    public function getBasicForm($id = null) {

        $builder = new DoctrineAnnotationBuilder($this->entityManager);
        $form = $builder->createForm($this->entity);
        $form->setHydrator(new \DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity($this->getEntityManager()));
        return $form;
    }

    public function generateEntityForm($id = null) {

        $this->entityForm = $this->getBasicForm();

        if ($id) {
            $record = $this->getEntityManager()->getRepository($this->entity)->find($id);
        } else {
            $record = new $this->entity;
        }

        $this->entityForm->setObject($record);
        $this->entityForm->setAttribute('method', 'post');
        $this->entityForm->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'submit'
            )
        ));


        $this->entityForm->bind($record);
        return $this->entityForm;
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

            if ($filter["type"] == "lt") {
                $value = $filter['value'];
                $this->query->andWhere($this->query->expr()->lt("u." . $filter["key"], $this->query->expr()->literal($value)));
            }

            if ($filter["type"] == "gt") {
                $value = $filter['value'];
                $this->query->andWhere($this->query->expr()->gt("u." . $filter["key"], $this->query->expr()->literal($value)));
            }

            if ($filter["type"] == "between") {
                $value = $filter['value'];
                $value2 = $filter['value2'];
                $this->query->andWhere($this->query->expr()->between("u." . $filter["key"], $this->query->expr()->literal($value), $this->query->expr()->literal($value2)));
            }

            // echo $this->query->getDQL();
            //$where .= "u.".$filter["key"]." ".$filter["type"].$filter["value"];
            //$this->query->andWhere($where );
        }
    }

    public function queryOrder() {
        if ($this->orderBy && ($this->orderDirection == "DESC" || $this->orderDirection == "ASC")) {
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

    function getEntityForm() {
        if (!isset($this->entityForm)) {
            $this->generateEntityForm();
        }
        return $this->entityForm;
    }

    function setEntityForm($entityForm) {
        $this->entityForm = $entityForm;
    }

}
