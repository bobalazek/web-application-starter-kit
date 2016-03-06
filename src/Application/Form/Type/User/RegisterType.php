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
                ->create('profile', 'form', array(
                        'by_reference' => true,
                        'data_class' => 'Application\Entity\ProfileEntity',
                        'label' => false,
                ))
                    ->add('firstName', 'text', array(
                        'label' => 'First name',
                    ))
                    ->add('lastName', 'text', array(
                        'label' => 'Last name',
                        'required' => false,
                    ))
        );

        $builder->add('username', 'text', array(
            'label' => 'Username',
        ));
        $builder->add('email', 'email', array(
            'label' => 'Email',
        ));
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'first_name' => 'password',
            'second_name' => 'repeatPassword',
            'required' => false,
            'invalid_message' => 'The password is invalid',
        ));

        $builder->add('Save', 'submit', array(
            'attr' => array(
                'class' => 'btn-primary btn-lg btn-block',
            ),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Entity\UserEntity',
            'validation_groups' => array('register'),
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }
}
