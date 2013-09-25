AvListBundle
============

Easily make paginate and orderable list in Symfony2

1) Install
----------------

Add this in your composer :


        "appventus/avlist-bundle": "dev-master"


2) How to use it ?
----------------

a) Controller
----------------

This is a classic action to list an entity :


        use Symfony\Component\HttpFoundation\Request;

        class FooController extends Controller
        {m
                /**
                 * Lists all Foo entities.
                 *
                 */
                public function indexAction(Request $request)
                {
                    $em = $this->getDoctrine()->getEntityManager();

                    $queryBuilder = $em->createQueryBuilder()->select('f')
                            ->from('MyBundle:Foo', 'f');

                        return $this->render('MyBundle:Foo:index.html.twig', array(
                            'entities' => $queryBuilder->getQuery()->execute(),
                        ));
                }

Now, modify it to implement the AvList component :


        use Symfony\Component\HttpFoundation\Request;
        use AppVentus\ListBundle\Component\AvList;

        class FooController extends Controller
        {
                    /**
                     * Lists all Foo entities.
                     *
                     */
                    public function indexAction(Request $request)
                    {
                        $em = $this->getDoctrine()->getEntityManager();

                        $queryBuilder = $em->createQueryBuilder()->select('f')
                                ->from('MyBundle:Foo', 'f');
                        //create an AvList with our request, the array in second argument is optional, default maxPerPage value is 10.
                        $list = $this->get('av_list');
                        $list->addOption("maxPerPage", 20);
                        //and feed him with our query
                        $list->setQueryBuilder($queryBuilder);

                        //check if request is ajax, so load only the partial
                        if($this->get('request')->isXMLHttpRequest()){
                            return $this->render('MyBundle:Foo:indexPartial.html.twig', array(
                                'list' => $list,
                            ));
                        }else{
                            return $this->render('MyBundle:Foo:index.html.twig', array(
                                'list' => $list,
                            ));
                        }
                    }

b) View :
---------------

As you could see before, we now load two views, an index with the layout, and a partial witch just contain the list:

index.html.twig :

        {% extends "MyBundle::layout.html.twig" %}

        {% block content %}
        <h1>Foo list</h1>
            {% include 'MyBundle:Foo:indexPartial.html.twig' with {'list':list}%}
        {% endblock %}

Yeah, it's pretty minimalist, but it's very important

And now our Partial :

        {% if list.getPager.getNbResults > 0 %}
        <div id="{{list.id}}">
        <table class="table">
            <thead>
                <tr>
                    <th class="sortable" data-target="f.name">Name</th>
                    <th>Image</th>
                    <th class="sortable" data-target="f.price">Price</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
            {% for entity in list.pager %}
                <tr>
                    <td>
                            {{ entity.name }}
                    </td>
                    [...]
            {% endfor %}
            </tbody>
        </table>
        {% include 'AvListBundle:AvList:control.html.twig' with {'list':list}%}
        </div>
        {% else %}
        <p>No Foos :(</p>
        {% endif %}

And voila !

3) How it works ?
-----------------

In the partial view, you set on the th tag a class sortable and an id with the value of the field you want to search on. In our exemple, the entity Foo has the alias "f" in the query builder, so to sort by Foo's name, you set the following th id: "f.name".

4) Options
----------------

This bundle use the PagerFanta bundle to build paginator, more specificly the TwitterBootstrapView. Options are available for this view, and you can pass them as second argument when you instanciate AvList:

                        $list = $this->get('av_list');
                        $list->addOption("proximity", 3);
                        $list->addOption("previous_message", "Précédent");


Please refer to [PagerFanta](https://github.com/whiteoctober/Pagerfanta/blob/master/README.md) doc for more information


5) Multiple list in a page
----------------

If you need to have several lists components in a single page, you will have to define the route option (to specify a different url to call that the request one). Optionnally and if your route requires params, you obviously may pass an array through the route_parameters option.
Also, you won't be able to use the av_list service because of a singleton pattern issue (if you use the service, you always will use the same object and so on, you will overwrite the values instead of create a new list.



    /**
     * Lists all Help\FaqItem entities.
     *
     * @Route("/", name="admin_myentity_list")
     * @Route("/ajax/cat/{id}", name="ajax_admin_myentity_list_by_category")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($id = null)
    {
        $categories = Your_function();
        foreach ($categories as $key => $category) {
            $list = new AvList($request, $this->get('templating'));
            $list->setQueryBuilder($em->getRepository('MyBundle:MyEntity')->createQueryBuilder('e')->where('e.category = :category')->setParameter('category', $category));
            $list->setTemplate($partialTemplate);
            $list->setOptions(array(
                    'route'            => 'ajax_admin_myentity_list_by_category',
                    'route_parameters' => array('id' => $category->getId()),
                    'theme'            => 'range',
                    'maxPerPage'       => 5,
                    'container_class'  => $category->getId().'-myentity-list',
                    'container_id'     => $category->getId().'-myentity-list'
            ));
            $params["lists"][$category->getId()] = $list;
        }
    }



