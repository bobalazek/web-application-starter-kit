<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', [
            'label' => 'Title',
            'required' => false,
        ]);

        $builder->add('firstName', 'text', [
            'label' => 'First name',
        ]);

        $builder->add('middleName', 'text', [
            'label' => 'Middle name',
            'required' => false,
        ]);

        $builder->add('lastName', 'text', [
            'label' => 'Last name',
            'required' => false,
        ]);

        $builder->add(
            'gender',
            new GenderType(),
            [
                'label' => 'Gender',
                'required' => false,
            ]
        );

        $builder->add('birthdate', 'birthday', [
            'label' => 'Birthdate',
            'required' => false,
        ]);

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
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\ProfileEntity',
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
        return 'profile';
    }
}
