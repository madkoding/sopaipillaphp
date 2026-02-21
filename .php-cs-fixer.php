<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/Sopaipilla',
        __DIR__ . '/App',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->notPath('vendor');

return (new Config())
    ->setRules([
        '@PSR12'                  => true,
        'declare_strict_types'    => true,
        'no_unused_imports'       => true,
        'single_quote'            => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    ])
    ->setFinder($finder);
