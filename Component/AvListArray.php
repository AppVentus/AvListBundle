<?php

namespace AppVentus\ListBundle\Component;

/**
 * AvListArray class.
 */
class AvListArray extends AvList implements AvListInterface
{
    /** @var array */
    protected $data;

    /**
     * Set data.
     *
     * @param array $array
     *
     * @return AvListArray
     */
    public function setData($array)
    {
        $this->data = $array;

        return $this;
    }

    /**
     * Get array.
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
        $adapter = new \Pagerfanta\Adapter\ArrayAdapter($this->data);
        $pager = new \Pagerfanta\Pagerfanta($adapter);
        $pager->setMaxPerPage($this->options['max_per_page']);
        $pager->setCurrentPage($this->page);

        return $pager;
    }
}
