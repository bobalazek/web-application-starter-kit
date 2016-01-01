<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class ResetPasswordType extends AbstractType
{
    public $action;

    /**
     * @param string $action
     */
    public function __construct($action = '')
    {
        $this->action = $action;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->action == 'reset') {
            $builder->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'first_name' => 'password',
                'second_name' => 'repeatPassword',
                'required' => false,
                'invalid_message' => 'The password is invalid',
            ));
        } else {
            $builder->add('email', 'email');
        }

        $builder->add('submitButton', 'submit', array(
            'label' => 'Submit',
            'attr' => array(
                'class' => 'btn-primary btn-lg btn-block',
            ),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $action = $this->action;

        $resolver->setDefaults(array(
            'data_class' => 'Application\Entity\UserEntity',
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
            'validation_groups' => function () use ($action) {
                if ($action == 'reset') {
                    return array('resetPasswordReset');
                } else {
                    return array('resetPasswordRequest');
                }
            },
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return ''; // we don't want any input name wrapper
    }
}
