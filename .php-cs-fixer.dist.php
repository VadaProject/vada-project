<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        '@PHP81Migration' => true
    ])
    ->setFinder($finder)
;