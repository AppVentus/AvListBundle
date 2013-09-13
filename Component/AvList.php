<?php
namespace AppVentus\ListBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine as Templating;

/**
 * AvList class
 *
 * @package default
 * @author Paul Andrieux, AppVentus
 * @author Leny Bernard, AppVentus
 **/
class AvList
{
    protected $queryBuilder;
    public $orderby;
    public $way = 'ASC';
    public $pager;
    public $request;
    public $templating;
    public $page = 1;
    public $options;
    public $template;

    /**
     * This is the constructor. You can also call the service av_list which is a way easier
     *
     * @return string
     */
    public function __construct(Request $request, Templating $templating)
    {
        $this->request    = $request;
        $this->templating = $templating;

        $this->orderby    = $this->request->query->get('orderby');
        $this->way        = $this->request->query->get('way') ? $this->request->query->get('way') : $this->way;
        $this->page       = $this->request->query->get('page') ? $this->request->query->get('page') : $this->page;
        $this->template   = 'AvListBundle:AvList:list.html.twig';
        $requestParameters = $this->request->getMethod() === 'GET' ? $this->request->query->all() : $this->request->request->all();
        $this->options    = array(
            'id'               => 'sortable-list',
            'class'            => 'sortable-list',
            'container_id'     => 'list-container',
            'update_id'        => null,
            'route'            => $this->request->get('_route'),
            'route_parameters' => array_merge($this->request->get('_parameters', array()), $requestParameters),
            'container_class'  => 'list-container',
            'maxPerPage'       => 10,
            'proximity'        => 3
        );

    }

    /**
     * Set options.
     *
     * @param array $options Array of options.
     * @return AvList
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }


    /**
     * Set option.
     *
     * @param string $name Name of option.
     * @param string $value Value of option.
     */
    public function addOption($name, $value)
    {
        $this->option[$name] = $value;
    }

    /**
     * @param array $queryBuilder Array of options.
     * @return AvList
     */
    public function setQueryBuilder($queryBuilder)
    {
        if ($this->orderby && $this->way) {
            $queryBuilder->orderby($this->orderby, $this->way);
        }
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    /**
     * Build and get a pager computed by the options and request
     *
     * @return string
     */
    public function getPager()
    {
        if (!$this->pager) {
            $adapter = new DoctrineORMAdapter($this->queryBuilder->getQuery());
            $pager   = new PagerFanta($adapter);
            $pager->setMaxPerPage($this->options['maxPerPage']);
            $pager->setCurrentPage($this->page);
            $this->pager = $pager;
        }

        return $this->pager;
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
                    $paginatorControll = $this->templating->render(
                        'AvListBundle:AvList:rangeCursor.html.twig',
                        array(
                            'paginator'    => $this->pager,
                            'route'        => isset($this->options['route']) ? $this->options['route'] : $this->request->get('_route'),
                            'route_parameters' => $this->options['route_parameters'] ? $this->options['route_parameters'] : $this->request->get('_parameters', array()),
                            'orderBy'      => $this->orderby,
                            'way'          => $this->way,
                            'update_id'    => $this->options['update_id'] ? : null,
                            'container_id' => $this->options['container_id'] ? : ''
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

            $view = new TwitterBootstrapView();
            $paginatorControll = $view->render($this->pager, $routeGenerator, $this->options);
        }

        return $paginatorControll;
    }

    /**
     * Get way we have to sort results
     *
     * @return string
     */
    public function getWay()
    {
        return $this->way == 'ASC' ? 'DESC' : 'ASC';
    }

    /**
     * Get id option
     *
     * @return string
     */
    public function getId()
    {
        return $this->options['id'];
    }

    /**
     * Get container option
     *
     * @return string
     */
    public function getContainer()
    {
        return $this->options['container'];
    }

    /**
     * Get class option
     *
     * @return string
     */
    public function getClass()
    {
        return $this->options['class'];
    }

    /**
     * Set template.
     *
     * @param string $template Template.
     * @return AvList
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
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
}
