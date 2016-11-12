<?php

namespace CdiDataGrid\Source\Doctrine;


use \DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;
/**
 * Description of Crud
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
trait CrudTrait {

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
     * Datagrid Entity Name
     * 
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;
    
  function getEm() {
        if (!isset($this->em)) {
            throw new \CdiDataGrid\Exception\EntityManagerNoSetException();
        }
        return $this->em;
    }

    function setEm(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
        return $this;
    }

    function getEntityName() {
        if (!isset($this->entityName)) {
            throw new \Exception("No EntityName set");
        }
        return $this->entityName;
    }

    function setEntityName($entityName) {
        $this->entityName = $entityName;
        return $this;
    }

    function getRepository() {
        if (isset($this->repository)) {
            $this->setRepository($this->getEm()->getRepository($this->getEntityName()));
        }
        return $this->repository;
    }

    function setRepository(\Doctrine\ORM\EntityRepository $repository) {
        $this->repository = $repository;
        return $this;
    }
    
    
    public function getBasicForm($id = null) {
        $builder = new DoctrineAnnotationBuilder($this->getEm());
        $form = $builder->createForm($this->entityName);
        $form->setHydrator(new \DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity($this->getEm()));
        return $form;
    }

    public function generateEntityForm($id = null) {

        $this->entityForm = $this->getBasicForm();

        if ($id) {
            $record = $this->getEm()->getRepository($this->entity)->find($id);
        } else {
            $record = new $this->entityName;
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

    public function delRecord($id) {
        $record = $this->getEm()->getRepository($this->entity)->find($id);
        $this->getEm()->remove($record);
        $this->getEm()->flush();
    }

    public function viewRecord($id) {
        $record = $this->getEm()->getRepository($this->entity)->find($id);
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
            $this->getEm()->persist($record);
            $this->getEm()->flush();
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
            $this->getEm()->persist($record);
            $this->getEm()->flush();
            $argv["record"] = $record;
            $this->getEventManager()->trigger(__FUNCTION__ . '_post', $this, $argv);
            return true;
        } else {
            return false;
        }
    }

}
