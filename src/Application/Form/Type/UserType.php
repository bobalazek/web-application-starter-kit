<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class UserType extends AbstractType
{
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'profile',
            new ProfileType(),
            [
                'label' => false,
            ]
        );

        $builder->add('username', 'text', [
            'required' => false,
        ]);
        $builder->add('email', 'email');
        $builder->add('plainPassword', 'repeated', [
            'type' => 'password',
            'first_name' => 'password',
            'second_name' => 'repeatPassword',
            'required' => false,
            'invalid_message' => 'The password fields must match.',
            'first_options' => [
                'label' => 'New password',
            ],
            'second_options' => [
                'label' => 'Repeat new Password',
            ],
        ]);

        $rolesChoices = $this->app['user_system_options']['roles'];
        if (!$this->app['security']->isGranted('ROLE_SUPER_ADMIN')) {
            // Only the super admin should be able to set other users to admins and super admins!
            unset($rolesChoices['ROLE_SUPER_ADMIN']);
            unset($rolesChoices['ROLE_ADMIN']);
        }

        $builder->add('roles', 'choice', [
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => $rolesChoices,
        ]);

        $builder->add('enabled', 'checkbox', [
            'label' => 'Is enabled?',
            'required' => false,
        ]);
        $builder->add('locked', 'checkbox', [
            'label' => 'Is locked?',
            'required' => false,
        ]);

        $builder->add('submitButton', 'submit', [
            'label' => 'Save',
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
            'validation_groups' => function (FormInterface $form) {
                $user = $form->getData();
                $validationGroups = [];

                if ($user->isLocked()) {
                    $validationGroups[] = 'isLocked';
                }

                if ($user->getId()) {
                    $validationGroups[] = 'edit';
                } else {
                    $validationGroups[] = 'new';
                }

                return $validationGroups;
            },
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
            'cascade_validation' => true,
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
