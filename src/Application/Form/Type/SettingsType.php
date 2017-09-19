<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Silex\Application;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class SettingsType extends AbstractType
{
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('foo', TextareaType::class, [
            'label' => 'Foo?',
            'required' => false,
            'data' => $this->app['settings']['foo'],
            'attr' => [
                'data-help-text' => 'Is it really foo?',
            ],
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
        $resolver->setDefaults([
            'data_class' => 'CoreBundle\Entity\User',
            'validation_groups' => ['settings'],
            'cascade_validation' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'settings';
    }
}
