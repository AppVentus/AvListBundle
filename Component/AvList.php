<?php
namespace AppVentus\ListBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\ORM\QueryBuilder;

/**
 * AvList abstract class.
 */
abstract class AvList
{
    /** @var Request */
    protected $request;
    /** @var TwigEngine */
    protected $templating;
    /** @var string */
    protected $template;
    /** @var array */
    protected $options;
    /** @var int */
    protected $page;
    /** @var string */
    protected $sort;
    /** @var string */
    protected $order;
    /** @var array */
    protected $columns = array();

    /**
     *
     * @param Request      $request      The request.
     * @param TwigEngine   $templating   The templating engine.
     * @param QueryBuilder $queryBuilder The queryBuilder.
     * @param string       $template     Template to render.
     * @param array        $options      Array of options.
     */
    public function __construct(Request $request, TwigEngine $templating, $qb, $template = null, array $options = array())
    {
        $this->request    = $request;
        $this->templating = $templating;
        $this->template   = (is_string($template)) ? $template : 'AvListBundle:AvList:list.html.twig';
        $this->page       = ($this->request->query->get('page')) ? $this->request->query->get('page') : 1;
        $this->sort       = $this->request->query->get('sort');
        $this->order      = $this->request->query->get('order') ? $this->request->query->get('order') : 'ASC';

        $this->setData($qb);
        $this->setOptions($options);
    }

    /**
     * Get template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set options.
     *
     * @param array $options Array of options.
     * @return AvList
     */
    public function setOptions(array $options)
    {
        $requestParameters = $this->request->isMethod('GET') ? $this->request->query->all() : $this->request->request->all();

        $defaultOptions    = array(
            'id'               => 'sortable-list',
            'class'            => 'sortable-list',
            'container_id'     => 'list-container',
            'container_class'  => 'list-container',
            'update_id'        => null,
            'route'            => $this->request->get('_route'),
            'route_parameters' => array_merge($this->request->get('_parameters', array()), $requestParameters),
            'max_per_page'     => 10,
            'proximity'        => 3,
        );

        $this->options = array_merge($defaultOptions, $options);

        return $this;
    }

    /**
     * Set option.
     *
     * @param string $name  Name of option.
     * @param string $value Value of option.
     */
    public function addOption($name, $value)
    {
        $this->option[$name] = $value;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get option.
     *
     * @param string $name Option name.
     * @return mixed
     */
    public function getOption($name)
    {
        return $this->options[$name];
    }

    /**
     * Get a paginator control
     *
     * @return string
     */
    public function getControl()
    {
        if (array_key_exists('theme', $this->options)) {
            switch ($this->options['theme']) {
                case 'range':
                    $paginatorControl = $this->templating->render(
                        'AvListBundle:AvList:rangeCursor.html.twig',
                        array(
                            'paginator'        => $this->getPager(),
                            'route'            => isset($this->options['route']) ? $this->options['route'] : $this->request->get('_route'),
                            'route_parameters' => $this->options['route_parameters'] ? $this->options['route_parameters'] : $this->request->get('_parameters', array()),
                            'sort'             => $this->sort,
                            'order'            => $this->order,
                            'update_id'        => $this->options['update_id'] ? : null,
                            'container_id'     => $this->options['container_id'] ? : ''
                        ));
                    break;
                default:
                    throw new \Exception('Theme not supported, available is : \'range\'');
                    break;
            }
        } else {
            //TODO : make it possible to have several list with this TwitterBootstrapView paginator component
            $routeGenerator = function($page) {
                return $this->request->create($this->request->getUri(), 'GET', array('page' => $page))->getUri();
            };

            $view = new \Pagerfanta\View\TwitterBootstrapView();
            $paginatorControl = $view->render($this->getPager(), $routeGenerator, $this->options);
        }

        return $paginatorControl;
    }

    /**
     * Get the ordering expression.
     *
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Get the ordering direction.
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Invert the ordering direction.
     *
     * @return string
     */
    public function toggleOrder()
    {
        return $this->getOrder() === 'ASC' ? 'DESC' : 'ASC';
    }

    /**
     * Add column
     *
     * @param string  $id         The name of the column of the function of the object
     * @param string  $filter     The twig filters you want to apply : array('name' => 'localizeddate', 'params' => array('medium', null))
     * @param string  $labelId    The id of the label
     * @param boolean $sortable   Can this column be sorted
     * @param string  $sortableId The identifier of the sort
     *
     * @return array The array of a column definition
     */
    public function addColumn($id, $filters = array(), $labelId = null, $sortable = false, $sortableId = null)
    {
        //by default the id is used for the column label
        if ($labelId === null) {
            $labelId = $id;
        }

        //if sortable, then sortableId is mandatory
        if ($sortable === true) {
            if ($sortableId === null) {
                throw new \Exception('The column '.$id.' is sortable but has no $sortableId defined');
            }
        }

        $column = array(
            'id'          => $id,
            'filters'     => $filters,
            'label'       => $labelId,
            'sortable'    => $sortable,
            'sortable_id' => $sortableId
        );

        $this->columns[] = $column;

        return $this;
    }

    /**
     * Get the columns to display
     * @return multitype:string
     */
    public function getColumns()
    {

        return $this->columns;
    }
}
