<?php

namespace Application\Twig;

use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class FileExtension extends \Twig_Extension
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
        return 'application/file';
    }

    /**
     * @return array
     */
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

    /**
     * @return string|false
     */
    public function fileContents($path)
    {
        $path = ROOT_DIR.'/'.$path;

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return false;
    }
}
