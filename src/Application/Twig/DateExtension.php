<?php

namespace Application\Twig;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class DateExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'application/date';
    }

    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('age', array($this, 'age')),
        );
    }

    /**
     * @return string
     */
    public function age($date)
    {
        if (!($date instanceof \DateTime)) {
            $date = new \DateTime($date);
        }

        return $date->diff(new \DateTime())->format('%y');
    }
}
