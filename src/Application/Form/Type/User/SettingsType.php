<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Application\Form\Type\ProfileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
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
            ProfileType::class,
            [
                'label' => false,
            ]
        );

        $builder->add('username', TextType::class, [
            'label' => 'Username',
        ]);
        $builder->add('email', EmailType::class);

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
            'data_class' => 'Application\Entity\UserEntity',
            'validation_groups' => ['settings'],
            'cascade_validation' => true,
        ]);
    }
}
