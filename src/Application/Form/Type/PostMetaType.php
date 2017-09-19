<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class PostMetaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('key', TextType::class);
        $builder->add('value', TextareaType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\PostMetaEntity',
            'validation_groups' => ['new_and_edit'],
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'postMeta';
    }
}
