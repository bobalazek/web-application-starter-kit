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
        return [
            new \Twig_SimpleFunction(
                'form_has_errors',
                [
                    $this,
                    'formHasErrors',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'form_value',
                [
                    $this,
                    'formValue',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'form_checkbox_value',
                [
                    $this,
                    'formCheckboxValue',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @param bool
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
     * @return bool
     */
    public function formCheckboxValue($form)
    {
        return $form->vars['checked']
            ? true
            : false
        ;
    }
}
