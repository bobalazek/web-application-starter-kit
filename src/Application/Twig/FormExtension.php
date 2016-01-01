<?php

namespace Application\Twig;

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
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'form_has_errors',
                array(
                    $this,
                    'formHasErrors',
                ),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'form_value',
                array(
                    $this,
                    'formValue',
                ),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'form_checkbox_value',
                array(
                    $this,
                    'formCheckboxValue',
                ),
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
