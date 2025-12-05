<?php

$fileHeaderComment = <<<COMMENT
This file is part of the EcommitDoctrineEntitiesGeneratorBundle package.

(c) E-commit <contact@e-commit.fr>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('tests/App/var')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@DoctrineAnnotation' => true,
        '@PHP8x1Migration' => true,
        '@PHP8x1Migration:risky' => true,
        '@PHPUnit10x0Migration:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'fopen_flags' => true,
        'header_comment' => ['header' => $fileHeaderComment, 'separate' => 'both'],
        'linebreak_after_opening_tag' => true,
        'mb_str_functions' => true,
        'no_php4_constructor' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'protected_to_private' => false,
        'phpdoc_to_comment' => ['ignored_tags' => ['psalm-suppress']],
    ])
    ->setFinder($finder)
;
