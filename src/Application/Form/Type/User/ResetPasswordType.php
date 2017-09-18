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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->action == 'reset') {
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
        } else {
            $builder->add('email', 'email');
        }

        $builder->add('submitButton', 'submit', [
            'label' => 'Submit',
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
        $action = $this->action;

        $resolver->setDefaults([
            'data_class' => 'Application\Entity\UserEntity',
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
            'validation_groups' => function () use ($action) {
                if ($action == 'reset') {
                    return ['reset_password_reset'];
                } else {
                    return ['reset_password_request'];
                }
            },
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
