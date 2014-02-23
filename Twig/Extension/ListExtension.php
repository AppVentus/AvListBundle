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
            'list_widget' => new \Twig_Function_Method($this, 'listWidget', array('is_safe' => array('html')))
        );
    }

    /**
     * register twig filters
     */
    public function getFilters()
    {
        return array(
            'list_value_render' => new \Twig_SimpleFilter(
                'listValueRender',
                array($this, 'listValueRender'),
                array(
                    'is_safe'           => array('html'),
                    'needs_environment' => true,
                    'needs_context'     => true,
                )
            )
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
     * Render a value for a column with the specific filter
     * @description :
     * 1. Check filters given (array? empty ?)
     * 2. Create the string with given var and apply each filter
     * 3. Return the string as template or the value directly if no filters
     *
     * return string
     */
    public function listValueRender(\Twig_Environment $env, $context = array(), $value, $filters = null)
    {
        if (is_array($filters) && !empty($filters)) {
            $response = "{{ ".key(compact('value'));
            foreach ($filters as $key => $filter) {
                $response .= "|".$filter['name'];
                if (!empty($filter['params'])) {
                    $response .= "('".implode(', ', $filter['params'])."')";
                }
            }
            $response .= " }}";
            $twigEnv = new \Twig_Environment(new \Twig_Loader_String());
            $value = $twigEnv->render(
              $response
            );
        }

        return $value;

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
