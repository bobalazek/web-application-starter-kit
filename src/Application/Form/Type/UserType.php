<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Silex\Application;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $options['app'];

        $builder->add(
            'profile',
            ProfileType::class,
            [
                'label' => false,
            ]
        );

        $builder->add('username', TextType::class, [
            'required' => false,
        ]);
        $builder->add('email', EmailType::class);
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
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

        $rolesChoices = $app['user_system_options']['roles'];
        if (!$app['security.authorization_checker']->isGranted('ROLE_SUPER_ADMIN')) {
            // Only the super admin should be able to set other users to admins and super admins!
            unset($rolesChoices['ROLE_SUPER_ADMIN']);
            unset($rolesChoices['ROLE_ADMIN']);
        }

        $builder->add('roles', ChoiceType::class, [
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => array_flip($rolesChoices),
        ]);

        $builder->add('enabled', CheckboxType::class, [
            'label' => 'Is enabled?',
            'required' => false,
        ]);
        $builder->add('locked', CheckboxType::class, [
            'label' => 'Is locked?',
            'required' => false,
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
        $resolver->setRequired(['app']);
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
