<?php
namespace AppVentus\ListBundle\Twig\Extension;


/**
 * ListExtension extends Twig with page capabilities.
 *
 */
class ListExtension extends \Twig_Extension
{


    /**
     * contructor
     */
    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    /**
     * register twig functions
     */
    public function getFunctions()
    {
        return array(
            'list_widget'  => new \Twig_Function_Method($this, 'listWidget', array('is_safe' => array('html'))),
        );
    }


    /**
     * render actions for a widget
     */
    public function listWidget($list, $extra = array())
    {
        return $this->twig->render("AvListBundle:Avlist:container.html.twig", array('list' => $list, 'extra' => $extra));
    }


    /**
     * get extension name
     */
    public function getName()
    {
        return 'list';
    }

}
