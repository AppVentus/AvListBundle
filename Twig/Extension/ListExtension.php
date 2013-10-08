<?php
namespace AppVentus\ListBundle\Twig\Extension;

/**
 * ListExtension extends Twig with page capabilities.
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
     * Render actions for a widget
     *
     * return string
     */
    public function listWidget($list, $extra = array())
    {
        return $this->twig->render('AvListBundle:AvList:container.html.twig', array('list' => $list, 'extra' => $extra));
    }


    /**
     * Get extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'list';
    }
}
