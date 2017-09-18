<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');

        $builder->add('image', 'file', [
            'required' => false,
        ]);
        $builder->add('removeImage', 'checkbox', [
            'required' => false,
            'data' => false,
            'label' => 'Remove image?',
            'attr' => [
                'data-help-text' => 'Should the image be removed (goes into effect after the save)?',
            ],
        ]);

        $builder->add('content', 'textarea', [
            'required' => false,
            'attr' => [
                'class' => 'html-editor',
            ],
        ]);

        $builder->add('user', 'entity', [
            'required' => false,
            'empty_value' => false,
            'class' => 'Application\Entity\UserEntity',
            'attr' => [
                'class' => 'select-picker',
                'data-live-search' => 'true',
            ],
        ]);

        $builder->add('postMetas', 'collection', [
            'type' => new PostMetaType(),
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'prototype' => true,
            'cascade_validation' => true,
            'error_bubbling' => false,
            'by_reference' => false,
        ]);

        $builder->add('submitButton', 'submit', [
            'label' => 'Save',
            'attr' => [
                'class' => 'btn-primary btn-lg btn-block',
            ],
        ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\PostEntity',
            'validation_groups' => ['new_and_edit'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'post';
    }
}
