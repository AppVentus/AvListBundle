<?php
namespace AppVentus\ListBundle\Component;

use Doctrine\ORM\QueryBuilder;

/**
 * AvListQueryBuilder class
 */
class AvListQueryBuilder extends AvList implements AvListInterface
{
    /**
     * Set data.
     *
     * @param array $queryBuilder QueryBuilder.
     * @return AvListQueryBuilder
     */
    public function setData(QueryBuilder $qb)
    {
        if ($this->sort && $this->order) {
            $qb->orderby($this->sort, $this->order);
        }
        $this->data = $qb;

        return $this;
    }

    /**
     * Get query builder.
     *
     * @return QueryBuilder
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Build and get a pager computed by the options and request.
     *
     * @return PagerFanta
     */
    public function getPager()
    {
        $adapter = new \Pagerfanta\Adapter\DoctrineORMAdapter($this->data->getQuery());
        $pager   = new \Pagerfanta\Pagerfanta($adapter);
        $pager->setMaxPerPage($this->options['max_per_page']);
        $pager->setCurrentPage($this->page);

        return $pager;
    }
}
