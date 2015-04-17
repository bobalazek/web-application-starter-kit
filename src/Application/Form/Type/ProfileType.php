<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array(
            'label' => 'Title',
            'required' => false,
        ));

        $builder->add('firstName', 'text', array(
            'label' => 'First name',
        ));

        $builder->add('middleName', 'text', array(
            'label' => 'Middle name',
            'required' => false,
        ));

        $builder->add('lastName', 'text', array(
            'label' => 'Last name',
            'required' => false,
        ));

        $builder->add(
            'gender',
            new \Application\Form\Type\GenderType(),
            array(
                'label' => 'Gender',
                'required' => false,
            )
        );

        $builder->add('birthdate', 'birthday', array(
            'label' => 'Birthdate',
            'required' => false,
        ));

        $builder->add('image', 'file', array(
            'required' => false,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Entity\ProfileEntity',
            'validation_groups' => array('newAndEdit'),
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ));
    }

    public function getName()
    {
        return 'profile';
    }
}
