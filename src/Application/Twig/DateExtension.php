<?php

namespace Application\Twig;

use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class DateExtension extends \Twig_Extension
{
    private $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application/date';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'age' => new \Twig_Filter_Method($this, 'age'),
        );
    }

    /**
     * @return string
     */
    public function age($date)
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        return $date->diff(new \DateTime())->format('%y');
    }
}
