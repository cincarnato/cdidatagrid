<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CdiDataGrid\DataGrid\Source\Doctrine;

use Zend\Paginator\Adapter\AdapterInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

/**
 * Description of Paginator
 *
 * @author cincarnato
 */ 
class Paginator2 implements AdapterInterface {
    //put your code here

    /**
     *
     * @var QueryBuilder
     */
    protected $query = null;
    protected $items;

    function __construct($query) {
        if ($query instanceof QueryBuilder) {
            $query = $query->getQuery();
        }

        $this->query = $query;
    }

    public function getItems($offset, $itemCountPerPage) {

        $result = $this->cloneQuery($this->query)
                ->setMaxResults($itemCountPerPage)
                ->setFirstResult($offset)
                ->getArrayResult($this->query->getHydrationMode());
        $this->items = new \ArrayIterator($result);
        
        foreach($this->items as $item){
            var_dump($item);
        }
        
        echo "items . ";
        return $this->items;
        
    }

    /**
     * Clones a query.
     *
     * @param Query $query The query.
     *
     * @return Query The cloned query.
     */
    private function cloneQuery(Query $query) {
        /* @var $cloneQuery Query */
        $cloneQuery = clone $query;

        $cloneQuery->setParameters(clone $query->getParameters());

        foreach ($query->getHints() as $name => $value) {
            $cloneQuery->setHint($name, $value);
        }

        return $cloneQuery;
    }

    public function count() {
        echo " - ".count($this->items);
        return count($this->items);
    }

    public function getQuery() {
        return $this->query;
    }

    public function setQuery(QueryBuilder $query) {
        $this->query = $query;
    }

}

?>
