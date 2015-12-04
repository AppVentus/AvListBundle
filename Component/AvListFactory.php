<?php

namespace AppVentus\ListBundle\Component;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;

/**
 * AvListFactory.
 *
 * service id : av_list
 */
class AvListFactory
{
    /** @var Request */
    protected $request;
    /** @var TwigEngine */
    protected $templating;
    /** @var Translator */
    protected $translator;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request    The request.
     * @param \Symfony\Bundle\TwigBundle\TwigEngine     $templating The templating engine.
     * @param Translator                                $translator The translator service
     */
    public function __construct(Request $request, TwigEngine $templating, $translator)
    {
        $this->request = $request;
        $this->templating = $templating;
        $this->translator = $translator;
    }

    /**
     * Return AvList.
     *
     * @param mixed  $data     The data.
     * @param string $template Template to render.
     * @param array  $options  Array of options.
     *
     * @return AvList
     */
    public function getList($data, $sort, $order, $template, array $options = [])
    {
        //get the translations if they are not given
        if (!isset($options['prev_message'])) {
            $options['prev_message'] = $this->translator->trans('prev_message', [], 'av_list');
        }
        if (!isset($options['next_message'])) {
            $options['next_message'] = $this->translator->trans('next_message', [], 'av_list');
        }

        switch (true) {
            case $data instanceof \Doctrine\ORM\QueryBuilder:
                $list = new AvListQueryBuilder($this->request, $this->templating, $data, $sort, $order, $template, $options);
                break;
            case is_array($data):
                $list = new AvListArray($this->request, $this->templating, $data, $sort, $order, $template, $options);
                break;

            default:
                break;
        }

        return $list;
    }
}
