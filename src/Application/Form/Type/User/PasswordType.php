<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as SymfonyPasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class PasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPassword', SymfonyPasswordType::class, [
            'label' => 'Current password',
        ]);

        $builder->add('plainPassword', RepeatedType::class, [
            'type' => SymfonyPasswordType::class,
            'first_name' => 'newPassword',
            'second_name' => 'newPasswordRepeat',
            'invalid_message' => 'The password fields must match.',
            'first_options' => [
                'label' => 'New password',
            ],
            'second_options' => [
                'label' => 'Repeat new Password',
            ],
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
            'data_class' => 'Application\Entity\UserEntity',
            'validation_groups' => ['settings_password'],
        ]);
    }
}
