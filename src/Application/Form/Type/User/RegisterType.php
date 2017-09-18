<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
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
                ->create('profile', 'form', [
                        'by_reference' => true,
                        'data_class' => 'Application\Entity\ProfileEntity',
                        'label' => false,
                ])
                    ->add('firstName', 'text', [
                        'label' => 'First name',
                    ])
                    ->add('lastName', 'text', [
                        'label' => 'Last name',
                        'required' => false,
                    ])
        );

        $builder->add('username', 'text', [
            'label' => 'Username',
        ]);
        $builder->add('email', 'email', [
            'label' => 'Email',
        ]);
        $builder->add('plainPassword', 'repeated', [
            'type' => 'password',
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

        $builder->add('submitButton', 'submit', [
            'label' => 'Register',
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
            'validation_groups' => ['register'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
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
