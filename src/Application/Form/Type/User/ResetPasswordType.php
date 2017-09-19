<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class ResetPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $action = $options['action'];

        if ($action == 'reset') {
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
        } else {
            $builder->add('email', EmailType::class);
        }

        $builder->add('submitButton', SubmitType::class, [
            'label' => 'Submit',
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
        $resolver->setRequired(['action']);
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\UserEntity',
            'validation_groups' => function () use ($resolver) {
                $action = $resolver->offsetGet('action');
                if ($action == 'reset') {
                    return ['reset_password_reset'];
                } else {
                    return ['reset_password_request'];
                }
            },
        ]);
    }
}
