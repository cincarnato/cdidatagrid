<?php


namespace CdiDataGrid\DataGrid\Source\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\WhereInWalker;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\NoResultException;

/**
 * Description of Paginator
 *
 * @author cincarnato
 */
class Paginators extends \Doctrine\ORM\Tools\Pagination\Paginator implements \Countable, \IteratorAggregate  {
    /**
     * @var Query
     */
    private $query;

    /**
     * @var bool
     */
    private $fetchJoinCollection;

    /**
     * @var bool|null
     */
    private $useOutputWalkers;

    /**
     * @var int
     */
    private $count;

    /**
     * Constructor.
     *
     * @param Query|QueryBuilder $query               A Doctrine ORM query or query builder.
     * @param boolean            $fetchJoinCollection Whether the query joins a collection (true by default).
     */
    public function __construct($query, $fetchJoinCollection = true)
    {
        if ($query instanceof QueryBuilder) {
            $query = $query->getQuery();
        }

        $this->query = $query;
        $this->fetchJoinCollection = (Boolean) $fetchJoinCollection;
    }

    /**
     * Returns the query.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns whether the query joins a collection.
     *
     * @return boolean Whether the query joins a collection.
     */
    public function getFetchJoinCollection()
    {
        return $this->fetchJoinCollection;
    }

    /**
     * Returns whether the paginator will use an output walker.
     *
     * @return bool|null
     */
    public function getUseOutputWalkers()
    {
        return $this->useOutputWalkers;
    }

    /**
     * Sets whether the paginator will use an output walker.
     *
     * @param bool|null $useOutputWalkers
     *
     * @return $this
     */
    public function setUseOutputWalkers($useOutputWalkers)
    {
        $this->useOutputWalkers = $useOutputWalkers;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if ($this->count === null) {
            /* @var $countQuery Query */
            $countQuery = $this->cloneQuery($this->query);

            if ( ! $countQuery->hasHint(CountWalker::HINT_DISTINCT)) {
                $countQuery->setHint(CountWalker::HINT_DISTINCT, true);
            }

            if ($this->useOutputWalker($countQuery)) {
                $platform = $countQuery->getEntityManager()->getConnection()->getDatabasePlatform(); // law of demeter win

                $rsm = new ResultSetMapping();
                $rsm->addScalarResult($platform->getSQLResultCasing('dctrn_count'), 'count');

                $countQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Doctrine\ORM\Tools\Pagination\CountOutputWalker');
                $countQuery->setResultSetMapping($rsm);
            } else {
                $this->appendTreeWalker($countQuery, 'Doctrine\ORM\Tools\Pagination\CountWalker');
            }

            $countQuery->setFirstResult(null)->setMaxResults(null);

            try {
                $data =  $countQuery->getScalarResult();
                $data = array_map('current', $data);
                $this->count = array_sum($data);
            } catch(NoResultException $e) {
                $this->count = 0;
            }
        }

        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $offset = $this->query->getFirstResult();
        $length = $this->query->getMaxResults();

        if ($this->fetchJoinCollection) {
            $subQuery = $this->cloneQuery($this->query);

            if ($this->useOutputWalker($subQuery)) {
                $subQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Doctrine\ORM\Tools\Pagination\LimitSubqueryOutputWalker');
            } else {
                $this->appendTreeWalker($subQuery, 'Doctrine\ORM\Tools\Pagination\LimitSubqueryWalker');
            }

            $subQuery->setFirstResult($offset)->setMaxResults($length);

            $ids = array_map('current', $subQuery->getScalarResult());

            $whereInQuery = $this->cloneQuery($this->query);
            // don't do this for an empty id array
            if (count($ids) == 0) {
                return new \ArrayIterator(array());
            }

            $this->appendTreeWalker($whereInQuery, 'Doctrine\ORM\Tools\Pagination\WhereInWalker');
            $whereInQuery->setHint(WhereInWalker::HINT_PAGINATOR_ID_COUNT, count($ids));
            $whereInQuery->setFirstResult(null)->setMaxResults(null);
            $whereInQuery->setParameter(WhereInWalker::PAGINATOR_ID_ALIAS, $ids);

            $result = $whereInQuery->getResult($this->query->getHydrationMode());
        } else {
            $result = $this->cloneQuery($this->query)
                ->setMaxResults($length)
                ->setFirstResult($offset)
                ->getResult($this->query->getHydrationMode())
            ;
        }
        
        $rr =  new \ArrayIterator($result);
        echo "<pre>";
        foreach( $rr as $r){
          //  var_dump($r);
        }
       // var_dump(new \ArrayIterator($result));
        echo "</pre>";
        return new \ArrayIterator($result);
    }

    /**
     * Clones a query.
     *
     * @param Query $query The query.
     *
     * @return Query The cloned query.
     */
    private function cloneQuery(Query $query)
    {
        /* @var $cloneQuery Query */
        $cloneQuery = clone $query;

        $cloneQuery->setParameters(clone $query->getParameters());

        foreach ($query->getHints() as $name => $value) {
            $cloneQuery->setHint($name, $value);
        }

        return $cloneQuery;
    }

    /**
     * Determines whether to use an output walker for the query.
     *
     * @param Query $query The query.
     *
     * @return bool
     */
    private function useOutputWalker(Query $query)
    {
        if ($this->useOutputWalkers === null) {
            return (Boolean) $query->getHint(Query::HINT_CUSTOM_OUTPUT_WALKER) == false;
        }

        return $this->useOutputWalkers;
    }

    /**
     * Appends a custom tree walker to the tree walkers hint.
     *
     * @param Query $query
     * @param string $walkerClass
     */
    private function appendTreeWalker(Query $query, $walkerClass)
    {
        $hints = $query->getHint(Query::HINT_CUSTOM_TREE_WALKERS);

        if ($hints === false) {
            $hints = array();
        }

        $hints[] = $walkerClass;
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, $hints);
    }
    
}

?>
