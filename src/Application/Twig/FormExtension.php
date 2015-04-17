<?php

namespace Application\Twig;

use Silex\Application;

class FormExtension extends \Twig_Extension
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'application/form';
    }

    public function getFunctions()
    {
        return array(
            'form_has_errors' => new \Twig_Function_Method(
                $this,
                'formHasErrors',
                array(
                    'is_safe' => array('html'),
                )
            ),
            'form_value' => new \Twig_Function_Method(
                $this,
                'formValue',
                array(
                    'is_safe' => array('html'),
                )
            ),
            'form_checkbox_value' => new \Twig_Function_Method(
                $this,
                'formCheckboxValue',
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function formHasErrors($form)
    {
        return count($form->vars['errors']) > 0;
    }

    public function formValue($form, $fallback = null)
    {
        return $form->vars['value']
            ? $form->vars['value']
            : $fallback
        ;
    }

    public function formCheckboxValue($form)
    {
        return $form->vars['checked']
            ? true
            : false
        ;
    }
}
