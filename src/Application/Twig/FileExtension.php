<?php

namespace Application\Twig;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class FileExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'application/file';
    }

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'file_contents',
                [
                    $this,
                    'fileContents',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
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
