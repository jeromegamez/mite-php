<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(['src']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'header_comment' => ['header' => ''],
        'ordered_imports' => true,
        'phpdoc_align' => false,
        'phpdoc_order' => true,
    ])
    ->setFinder($finder);
