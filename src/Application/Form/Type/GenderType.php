<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class GenderType extends AbstractType
{
    const MALE = 'male';
    const FEMALE = 'female';

    /**
     * @param OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                self::MALE => 'Male',
                self::FEMALE => 'Female',
            ),
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gender';
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return 'gender';
    }
}
