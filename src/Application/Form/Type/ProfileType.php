<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'Title',
            'required' => false,
        ]);

        $builder->add('firstName', TextType::class, [
            'label' => 'First name',
        ]);

        $builder->add('middleName', TextType::class, [
            'label' => 'Middle name',
            'required' => false,
        ]);

        $builder->add('lastName', TextType::class, [
            'label' => 'Last name',
            'required' => false,
        ]);

        $builder->add(
            'gender',
            GenderType::class,
            [
                'label' => 'Gender',
                'required' => false,
            ]
        );

        $builder->add('birthdate', BirthdayType::class, [
            'label' => 'Birthdate',
            'required' => false,
        ]);

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
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\ProfileEntity',
            'validation_groups' => ['new_and_edit'],
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
