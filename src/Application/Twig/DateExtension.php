<?php

namespace Application\Twig;

use Silex\Application;

class DateExtension extends \Twig_Extension
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'application/date';
    }

    public function getFilters()
    {
        return array(
            'age' => new \Twig_Filter_Method($this, 'age'),
        );
    }

    public function age($date)
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        return $date->diff(new \DateTime())->format('%y');
    }
}
