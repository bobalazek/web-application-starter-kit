<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class PasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPassword', 'password', array(
            'label' => 'Current password',
        ));

        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'first_name' => 'newPassword',
            'second_name' => 'newPasswordRepeat',
            'invalid_message' => 'The password fields must match.',
            'first_options' => array(
                'label' => 'New password',
                'attr' => array(
                    'class' => 'password-meter-input',
                ),
            ),
            'second_options' => array(
                'label' => 'Repeat new Password',
            ),
        ));

        $builder->add('submitButton', 'submit', array(
            'label' => 'Save',
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
            'validation_groups' => array('settings_password'),
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
