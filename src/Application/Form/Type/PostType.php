<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class);

        $builder->add('image', FileType::class, [
            'required' => false,
        ]);
        $builder->add('removeImage', CheckboxType::class, [
            'required' => false,
            'data' => false,
            'label' => 'Remove image?',
            'attr' => [
                'data-help-text' => 'Should the image be removed (goes into effect after the save)?',
            ],
        ]);

        $builder->add('content', TextareaType::class, [
            'required' => false,
            'attr' => [
                'class' => 'html-editor',
            ],
        ]);

        $builder->add('user', EntityType::class, [
            'required' => false,
            'class' => 'Application\Entity\UserEntity',
            'attr' => [
                'class' => 'select-picker',
                'data-live-search' => 'true',
            ],
        ]);

        $builder->add('postMetas', CollectionType::class, [
            'entry_type' => PostMetaType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'prototype' => true,
            'error_bubbling' => false,
            'by_reference' => false,
        ]);

        $builder->add('submitButton', SubmitType::class, [
            'label' => 'Save',
            'attr' => [
                'class' => 'btn-primary btn-lg btn-block',
            ],
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\PostEntity',
            'validation_groups' => ['new_and_edit'],
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
