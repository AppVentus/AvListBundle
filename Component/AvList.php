<?php
namespace AppVentus\ListBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;
use Symfony\Component\DependencyInjection\ContainerAware;

class AvList extends ContainerAware
{
    protected $queryBuilder;
    public $orderby;
    public $way = "ASC";
    public $pager;
    public $request;
    public $page = 1;
    public $options = array('id'=>'sortable-list','maxPerPage'=>10, 'proximity'=>3);


    public function __construct(Request $request, $options = array()){
        $this->orderby = $request->query->get('orderby');
        $this->way = $request->query->get('way')?$request->query->get('way'):$this->way;
        $this->page = $request->query->get('page')?$request->query->get('page'):$this->page;
        $this->options = array_merge($this->options, $options);
        $this->request = $request;
    }
    public function setQueryBuilder($queryBuilder){
        if($this->orderby && $this->way){
            $queryBuilder->orderby($this->orderby, $this->way);
        }
        $this->queryBuilder = $queryBuilder;
    }

    public function getPager(){

        $adapter = new DoctrineORMAdapter($this->queryBuilder->getQuery());
        $pager = new PagerFanta($adapter);
        $pager->setMaxPerPage($this->options['maxPerPage']);
        $pager->setCurrentPage($this->page);
        $this->pager = $pager;
        return $pager;
    }
    public function getControll(){

        $routeGenerator = function($page) {
            return $this->request->create($this->request->getUri(), "GET", array("page"=>$page))->getUri();
            return $this->request->getUri()."?page=".$page;
        };
        $view = new TwitterBootstrapView();
        $paginatorControll = $view->render($this->pager, $routeGenerator, $this->options);
        return $paginatorControll;
    }
    public function getWay(){
        return $this->way=="ASC"?"DESC":"ASC";
    }
    public function getId(){
        return $this->options['id'];
    }
}
