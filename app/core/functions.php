<?php

if (! function_exists('rglob')) {
    function rglob($pattern = '*', $flags = 0, $path = '')
    {
        if (!$path && ($dir = dirname($pattern)) != '.') {
            if ($dir == '\\' || $dir == '/') {
                $dir = '';
            }

            return rglob(basename($pattern), $flags, $dir.'/');
        }
        $paths = glob($path.'*', GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($path.$pattern, $flags);
        foreach ($paths as $p) {
            $files = array_merge($files, rglob($pattern, $flags, $p.'/'));
        }

        return $files;
    }
}

if (! function_exists('dateRange')) {
    function dateRange($first, $last, $step = '+1 day', $format = 'Y-m-d')
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }
}

if (! function_exists('generateRandomString')) {
    function generateRandomString($length = 16, $onlyNumbers = false)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($onlyNumbers) {
            $characters = '0123456789';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

if (! function_exists('camelize')) {
    function camelize($word)
    {
        return preg_replace(
            '/(^|_)([a-z])/e', 'strtoupper("\\2")',
            $word
        );
    }
}

if (! function_exists('decamelize')) {
    function decamelize($word)
    {
        return preg_replace(
            '/(^|[a-z])([A-Z])/e',
            'strtolower(strlen("\\1") ? "\\1_\\2" : "\\2")',
            $word
        );
    }
}
