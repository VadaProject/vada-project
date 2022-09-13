<?php

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        '@PHP81Migration' => true
    ])
;
