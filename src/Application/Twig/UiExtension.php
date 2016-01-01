<?php

namespace Application\Twig;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class UiExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'application/ui';
    }

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array(
             new \Twig_SimpleFunction(
                'array_labels',
                array(
                    $this,
                    'arrayLabels',
                ),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'pagination',
                array(
                    $this,
                    'pagination',
                ),
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * @return string
     */
    public function arrayLabels($array = array())
    {
        if (!count($array)) {
            return '';
        }

        $output = '<ul class="list-inline">';
        foreach ($array as $one) {
            $output .= '<li>'.$one.'</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     * @return string
     */
    public function pagination($output)
    {
        return $output;
    }
}
