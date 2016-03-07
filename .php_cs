<?php

$finder = Symfony\CS\Finder::create()
    ->exclude('vendor')
    ->exclude('var')
;

return Symfony\CS\Config::create()
    ->finder($finder)
;
