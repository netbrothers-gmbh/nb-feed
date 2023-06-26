<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

require_once __DIR__ . '/vendor/autoload.php';

return (new Config())
    ->setRules([
        '@PSR12' => true,
        'no_extra_blank_lines' => true,
    ])
    ->setFinder(Finder::create()->in(__DIR__));
