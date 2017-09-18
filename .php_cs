<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('web/config.php')
;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
    ))
    ->setFinder($finder)
;
