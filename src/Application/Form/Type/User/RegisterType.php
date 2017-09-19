<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder
                ->create('profile', FormType::class, [
                        'by_reference' => true,
                        'data_class' => 'Application\Entity\ProfileEntity',
                        'label' => false,
                ])
                    ->add('firstName', TextType::class, [
                        'label' => 'First name',
                    ])
                    ->add('lastName', TextType::class, [
                        'label' => 'Last name',
                        'required' => false,
                    ])
        );

        $builder->add('username', TextType::class, [
            'label' => 'Username',
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'Email',
        ]);
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_name' => 'password',
            'second_name' => 'repeatPassword',
            'invalid_message' => 'The password fields must match.',
            'first_options' => [
                'label' => 'New password',
            ],
            'second_options' => [
                'label' => 'Repeat new Password',
            ],
        ]);

        $builder->add('submitButton', SubmitType::class, [
            'label' => 'Register',
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
            'validation_groups' => ['register'],
        ]);
    }
}
