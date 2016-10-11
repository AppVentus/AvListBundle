<?php

namespace AppVentus\ListBundle\Twig\Extension;

/**
 * ListExtension extends Twig with page capabilities.
 */
class ListExtension extends \Twig_Extension
{
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * register twig functions.
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('list_widget', [$this, 'listWidget'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * register twig filters.
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('list_value_render', [$this, 'listValueRender'], [
                    'is_safe'           => ['html'],
                    'needs_environment' => true,
                    'needs_context'     => true,
                ]), ];
    }

    /**
     * Render actions for a widget.
     *
     * return string
     */
    public function listWidget($list, $extra = [])
    {
        return $this->twig->render('AvListBundle:AvList:container.html.twig', ['list' => $list, 'extra' => $extra]);
    }

    /**
     * Render a value for a column with the specific filter.
     *
     * @description :
     * 1. Check filters given (array? empty ?)
     * 2. Create the string with given var and apply each filter
     * 3. Return the string as template or the value directly if no filters
     *
     * return string
     */
    public function listValueRender(\Twig_Environment $env, $context, $value, $filters = null)
    {
        if (is_array($filters) && !empty($filters)) {
            $response = '{{ value';
            foreach ($filters as $key => $filter) {
                $response .= '|'.$filter['name'];
                if (!empty($filter['params'])) {
                    $response .= "('".implode("', '", $filter['params'])."')";
                }
            }
            $response .= ' }}';

            //Creates a new twig environment
            $twig = new \Twig_Environment(new \Twig_Loader_Array(['response' => $response]));
            //Automatically inject all extensions to our new twig environment
            foreach ($this->twig->getExtensions() as $_ext) {
                $twig->addExtension($_ext);
            }
            $value = $twig->render('response', ['value' => $value]);
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
