<?php
namespace AppVentus\ListBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    public function __construct(ContainerInterface $container)
    {
        $this->request    = $container->get('request');
        $this->templating = $container->get('templating');
        $this->orderby    = $this->request->query->get('orderby');
        $this->way        = $this->request->query->get('way') ? $this->request->query->get('way') : $this->way;
        $this->page       = $this->request->query->get('page') ? $this->request->query->get('page') : $this->page;
        $this->template   = 'AvListBundle:AvList:list.html.twig';
        $this->options    = array(
            'id'              => 'sortable-list',
            'class'           => 'sortable-list',
            'container_id'    => 'list-container',
            'container_class' => 'list-container',
            'maxPerPage'      => 10,
            'proximity'       => 3
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

    public function getControl()
    {
        if (array_key_exists('theme', $this->options)) {
            switch ($this->options['theme']) {
                case 'range':
                    $paginatorControll = $this->templating->render(
                        'AvListBundle:AvList:rangeCursor.html.twig',
                        array(
                            'paginator'    => $this->pager,
                            'route'        => $this->request->get('_route'),
                            'orderBy'      => $this->orderby,
                            'way'          => $this->way,
                            'container_id' => $this->options['container_id'] ? : '',
                        ));
                    break;
                default:
                    throw new \Exception('Theme not supported, available is : \'range\'');
                    break;
            }
        } else {
            $routeGenerator = function($page) {
                return $this->request->create($this->request->getUri(), 'GET', array('page' => $page))->getUri();
            };

            $view = new TwitterBootstrapView();
            $paginatorControll = $view->render($this->pager, $routeGenerator, $this->options);
        }

        return $paginatorControll;
    }

    public function getWay()
    {
        return $this->way == 'ASC' ? 'DESC' : 'ASC';
    }

    public function getId()
    {
        return $this->options['id'];
    }
    public function getContainer()
    {
        return $this->options['container'];
    }
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
