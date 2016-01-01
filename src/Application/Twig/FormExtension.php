<?php

namespace Application\Twig;

use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class FormExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'application/form';
    }

    /**
     * @return array
     */
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

    /**
     * @param boolean
     */
    public function formHasErrors($form)
    {
        return count($form->vars['errors']) > 0;
    }

    /**
     * @return mixed
     */
    public function formValue($form, $fallback = null)
    {
        return $form->vars['value']
            ? $form->vars['value']
            : $fallback
        ;
    }

    /**
     * @return boolean
     */
    public function formCheckboxValue($form)
    {
        return $form->vars['checked']
            ? true
            : false
        ;
    }
}
