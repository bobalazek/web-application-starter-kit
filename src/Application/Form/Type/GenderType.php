<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class GenderType extends AbstractType
{
    const MALE = 'male';
    const FEMALE = 'female';

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                self::MALE => 'Male',
                self::FEMALE => 'Female',
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
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
