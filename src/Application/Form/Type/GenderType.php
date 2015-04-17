<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GenderType
    extends AbstractType
{
    const MALE = 'male';
    const FEMALE = 'female';

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                self::MALE => 'Male',
                self::FEMALE => 'Female',
            ),
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'gender';
    }

    public function getExtendedType()
    {
        return 'gender';
    }
}
