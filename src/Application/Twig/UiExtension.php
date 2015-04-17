<?php

namespace Application\Twig;

use Silex\Application;

class UiExtension extends \Twig_Extension
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'application/ui';
    }

    public function getFunctions()
    {
        return array(
            'array_labels' => new \Twig_Function_Method(
                $this,
                'arrayLabels',
                array(
                    'is_safe' => array('html'),
                )
            ),
            'pagination' => new \Twig_Function_Method(
                $this,
                'pagination',
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function arrayLabels($array = array())
    {
        if (! count($array)) {
            return '';
        }

        $output = '<ul class="list-inline">';
        foreach ($array as $one) {
            $output .= '<li>'.$one.'</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    public function pagination($output)
    {
        return $output;
    }
}
