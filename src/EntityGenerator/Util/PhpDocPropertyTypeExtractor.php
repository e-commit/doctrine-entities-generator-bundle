<?php

declare(strict_types=1);

/*
 * This file is part of the EcommitDoctrineEntitiesGeneratorBundle package.
 *
 * (c) E-commit <contact@e-commit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\Util;

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;

final class PhpDocPropertyTypeExtractor
{
    private Lexer $lexer;
    private PhpDocParser $phpDocParser;

    public function __construct()
    {
        if (class_exists(ParserConfig::class)) {
            $config = new ParserConfig([
                'lines' => false,
                'indexes' => false,
                'comments' => false,
            ]);

            $this->lexer = new Lexer($config);
            $constExprParser = new ConstExprParser($config);
            $typeParser = new TypeParser($config, $constExprParser);
            $this->phpDocParser = new PhpDocParser($config, $typeParser, $constExprParser);
        } else { // @legacy phpstan/phpdoc-parser v1
            $this->lexer = new Lexer();
            $constExprParser = new ConstExprParser();
            $typeParser = new TypeParser($constExprParser);
            $this->phpDocParser = new PhpDocParser($typeParser, $constExprParser);
        }
    }

    public function getPropertyPhpDocVarType(\ReflectionProperty $property): ?string
    {
        $docComment = $property->getDocComment();
        if (false === $docComment) {
            return null;
        }

        $tokens = new TokenIterator($this->lexer->tokenize($docComment));
        $phpDocNode = $this->phpDocParser->parse($tokens);

        $varTags = $phpDocNode->getVarTagValues(); // Read @var

        if ([] === $varTags) {
            return null;
        }

        $propVarName = '$'.$property->getName();

        foreach ($varTags as $varTag) {
            if ('' === $varTag->variableName || $varTag->variableName === $propVarName) {
                return (string) $varTag->type;
            }
        }

        // First @var

        return (string) $varTags[0]->type;
    }
}
