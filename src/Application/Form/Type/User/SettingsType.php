<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Application\Form\Type\ProfileType;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class SettingsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'profile',
            new ProfileType(),
            [
                'label' => false,
            ]
        );

        $builder->add('username', 'text', [
            'label' => 'Username',
        ]);
        $builder->add('email', 'email');

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
            'data_class' => 'Application\Entity\UserEntity',
            'validation_groups' => ['settings'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
            'cascade_validation' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }
}
