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
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'profile',
            new ProfileType(),
            array(
                'label' => false,
            )
        );

        $builder->add('username', 'text', array(
            'required' => false,
        ));
        $builder->add('email', 'email');
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'first_name' => 'password',
            'second_name' => 'repeatPassword',
            'required' => false,
            'invalid_message' => 'errors.user.password.invalidText',
        ));

        $builder->add('roles', 'choice', array(
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => $this->app['userSystemOptions']['roles'],
        ));

        $builder->add('enabled', 'checkbox', array(
            'label' => 'Is enabled?',
            'required' => false,
        ));
        $builder->add('locked', 'checkbox', array(
            'label' => 'Is locked?',
            'required' => false,
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
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Entity\UserEntity',
            'validation_groups' => function (FormInterface $form) {
                $user = $form->getData();
                $validationGroups = array();

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
