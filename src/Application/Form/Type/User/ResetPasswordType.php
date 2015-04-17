<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ResetPasswordType extends AbstractType
{
    public $action;

    public function __construct($action = '')
    {
        $this->action = $action;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->action == 'reset') {
            $builder->add('plainPassword', 'text');
        } else {
            $builder->add('email', 'email');
        }

        $builder->add('Submit', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $self = $this;

        $resolver->setDefaults(array(
            'data_class' => 'Application\Entity\UserEntity',
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
            'validation_groups' => function (FormInterface $form) use ($self) {
                $action = $self->action;

                if ($action == 'reset') {
                    return array('resetPasswordReset');
                } else {
                    return array('resetPasswordRequest');
                }
            },
        ));
    }

    public function getName()
    {
        return ''; // we don't want any input name wrapper
    }
}
