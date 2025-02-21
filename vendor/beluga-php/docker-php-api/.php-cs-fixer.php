<?php

$finder = \PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/src',
    ]);

$config = new \PhpCsFixer\Config();
return $config
    ->setRules([
        '@Symfony'                    => true,
        '@Symfony:risky'              => true,
        '@PHP56Migration:risky'       => true,
        '@PHP70Migration'             => true,
        '@PHP70Migration:risky'       => true,
        '@PHP71Migration'             => true,
        '@PHP71Migration:risky'       => true,
        '@PHP73Migration'             => true,
        '@PHP74Migration'             => true,
        '@PHP74Migration:risky'       => true,
        '@PHP80Migration'             => true,
        '@PHP80Migration:risky'       => true,
        'array_syntax'                => [
            'syntax' => 'short'
        ],
        'align_multiline_comment'     => true,
        'combine_consecutive_unsets'  => true,
        'declare_strict_types'        => true,
        'linebreak_after_opening_tag' => true,
        'modernize_types_casting'     => true,
        'native_function_invocation'  => true,
        'no_php4_constructor'         => true,
        'ordered_imports'             => true,
        'php_unit_strict'             => true,
        'phpdoc_line_span'            => true,
        'phpdoc_order'                => true,
        'strict_comparison'           => true,
        'strict_param'                => true,
    ])
    ->setRiskyAllowed(true)
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setFinder($finder)
;
