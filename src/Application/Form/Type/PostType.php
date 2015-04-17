<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');

        $builder->add('image', 'file', array(
            'required' => false,
        ));

        $builder->add('content', 'textarea', array(
            'required' => false,
            'attr' => array(
                'class' => 'html-editor',
            ),
        ));

        $builder->add('user', 'entity', array(
            'required' => false,
            'empty_value' => false,
            'class' => 'Application\Entity\UserEntity',
            'attr' => array(
                'class' => 'select-picker',
                'data-live-search' => 'true',
            ),
        ));

        $builder->add('Save', 'submit', array(
            'attr' => array(
                'class' => 'btn-primary btn-lg btn-block',
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Entity\PostEntity',
            'validation_groups' => array('newAndEdit'),
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ));
    }

    public function getName()
    {
        return 'post';
    }
}
