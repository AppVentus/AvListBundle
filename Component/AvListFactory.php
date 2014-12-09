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
     * @return AvList
     */
    public function getList($data, $sort, $order = 'ASC', $template, array $options = array())
    {
        //get the translations if they are not given
        if (!isset($options['prev_message'])) {
            $options['prev_message'] = $this->translator->trans('prev_message', array(), 'av_list');
        }
        if (!isset($options['next_message'])) {
            $options['next_message'] = $this->translator->trans('next_message', array(), 'av_list');
        }

        switch (true) {
            case $data instanceof \Doctrine\ORM\QueryBuilder:
                $list =  new AvListQueryBuilder($this->request, $this->templating,$data, $sort, $order,  $template, $options);
                break;
            case is_array($data):
                $list =  new AvListArray($this->request, $this->templating, $data, $sort, $order, $template, $options);
                break;

            default:
                break;
        }

        return $list;
    }
}
