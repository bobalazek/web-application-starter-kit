<?php

namespace Application\Tool;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class Helpers
{
    /**
     * @param string $pattern
     * @param int    $flags
     * @param string $path
     *
     * @return array
     */
    public static function rglob($pattern = '*', $flags = 0, $path = '')
    {
        if (!$path && ($dir = dirname($pattern)) != '.') {
            if ($dir == '\\' || $dir == '/') {
                $dir = '';
            }

            return self::rglob(basename($pattern), $flags, $dir.'/');
        }

        $paths = glob($path.'*', GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($path.$pattern, $flags);

        foreach ($paths as $p) {
            $files = array_merge($files, self::rglob($pattern, $flags, $p.'/'));
        }

        return $files;
    }
}
