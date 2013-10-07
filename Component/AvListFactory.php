<?php
namespace AppVentus\ListBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * AvListFactory
 *
 * service id : av_list
 */
class AvListFactory
{
    /** @var Request */
    protected $request;
    /** @var TwigEngine */
    protected $templating;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request    The request.
     * @param \Symfony\Bundle\TwigBundle\TwigEngine     $templating The templating engine.
     */
    public function __construct(Request $request, TwigEngine $templating)
    {
        $this->request    = $request;
        $this->templating = $templating;
    }

    /**
     * Return AvList.
     *
     * @param mixed  $data     The data.
     * @param string $template Template to render.
     * @param array  $options  Array of options.
     * @return AvList
     */
    public function getList($data, $template, array $options = array())
    {
        switch ($data) {
            case $data instanceof \Doctrine\ORM\QueryBuilder:
                $list =  new AvListQueryBuilder($this->request, $this->templating, $data, $template, $options);
                break;

            default:
                break;
        }

        return $list;
    }
}
