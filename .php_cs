<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('var')
;

return PhpCsFixer\Config::create()
    ->finder($finder)
;
