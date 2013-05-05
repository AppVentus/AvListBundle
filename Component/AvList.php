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
    public $way = "ASC";
    public $pager;
    public $request;
    public $templating;
    public $page = 1;
    public $options = array('id'=>'sortable-list','maxPerPage'=>10, 'proximity'=>3);

    public function __construct(ContainerInterface $container)
    {
        $this->request = $container->get('request');
        $this->templating = $container->get('templating');
        $this->orderby = $this->request->query->get('orderby');
        $this->way = $this->request->query->get('way')?$this->request->query->get('way'):$this->way;
        $this->page = $this->request->query->get('page')?$this->request->query->get('page'):$this->page;
    }

    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }
    public function setQueryBuilder($queryBuilder)
    {
        if ($this->orderby && $this->way) {
            $queryBuilder->orderby($this->orderby, $this->way);
        }
        $this->queryBuilder = $queryBuilder;
    }

    public function getPager()
    {
        $adapter = new DoctrineORMAdapter($this->queryBuilder->getQuery());
        $pager = new PagerFanta($adapter);
        $pager->setMaxPerPage($this->options['maxPerPage']);
        $pager->setCurrentPage($this->page);
        $this->pager = $pager;

        return $pager;
    }
    public function getControll()
    {
        if (array_key_exists('theme', $this->options)) {
            switch ($this->options['theme']) {
                case 'range':
                     $paginatorControll = $this->templating->render(
                        'AvAwesomeShorcutsBundle:Paginator:rangeCursor.html.twig',
                         array(
                            'paginator' => $this->pager,
                            'route' => $this->request->get("_route"),
                            'orderBy' => $this->orderby,
                            'way' => $this->way,
                            'update' => $this->options['update']?:"",
                        ));
                    break;
                default:
                    throw new \Exception("Theme not supported, available is : 'range'");
                    break;
            }
        } else {
            $routeGenerator = function($page) {
                return $this->request->create($this->request->getUri(), "GET", array("page"=>$page))->getUri();
                return $this->request->getUri()."?page=".$page;
            };
            $view = new TwitterBootstrapView();
            $paginatorControll = $view->render($this->pager, $routeGenerator, $this->options);

        }

        return $paginatorControll;
    }
    public function getWay()
    {
        return $this->way=="ASC"?"DESC":"ASC";
    }
    public function getId()
    {
        return $this->options['id'];
    }
}
