<?php

namespace Application\Twig;

use Silex\Application;

class FileExtension extends \Twig_Extension
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'application/file';
    }

    public function getFunctions()
    {
        return array(
            'file_contents' => new \Twig_Function_Method(
                $this,
                'fileContents',
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function fileContents($path)
    {
        $path = ROOT_DIR.'/'.$path;

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return false;
    }
}
