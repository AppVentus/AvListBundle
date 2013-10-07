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
     * @param array $queryBuilder Array of options.
     * @return AvListQueryBuilder
     */
    public function setData(QueryBuilder $qb)
    {
        if ($this->orderby && $this->way) {
            $qb->orderby($this->orderby, $this->way);
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
     * Build and get a pager computed by the options and request
     *
     * @return string
     */
    public function getPager()
    {
        $adapter = new Pagerfanta\Adapter\DoctrineORMAdapter($this->data->getQuery());
        $pager   = new Pagerfanta\Pagerfanta($adapter);
        $pager->setMaxPerPage($this->options['max_per_page']);
        $pager->setCurrentPage($this->page);

        return $pager;
    }
}
