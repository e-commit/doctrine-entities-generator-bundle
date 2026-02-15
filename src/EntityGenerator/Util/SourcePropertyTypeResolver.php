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

use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PhpParser\PhpVersion;

final class SourcePropertyTypeResolver
{
    private string $code;

    /** @var Node[] */
    private array $ast;

    private ?ClassLike $classNode;

    private NodeFinder $nodeFinder;

    public function __construct(string $code)
    {
        $this->code = $code;

        /* @legacy Support for nikic/php-parser v4 */
        if (class_exists(PhpVersion::class)) {
            $version = PhpVersion::fromString(\PHP_VERSION);
            $lexer = new Lexer\Emulative($version);
            $parser = new Parser\Php8($lexer, $version);
        } else {
            $lexer = new Lexer\Emulative([
                'usedAttributes' => [
                    'startFilePos',
                    'endFilePos',
                ],
            ]);
            $parser = new Parser\Php7($lexer);
        }

        $this->ast = $parser->parse($code) ?? [];
        $this->nodeFinder = new NodeFinder();

        /** @var ?ClassLike classNode */
        $classNode = $this->nodeFinder->findFirst(
            $this->ast,
            static fn (Node $node): bool => $node instanceof ClassLike && null !== $node->name
        );
        $this->classNode = $classNode;
    }

    public function getType(string $propertyName): ?string
    {
        if (!$this->classNode) {
            return null;
        }

        $propertyNode = $this->nodeFinder->findFirst(
            $this->classNode->stmts ?? [],
            static function (Node $node) use ($propertyName): bool {
                if (!$node instanceof Property) {
                    return false;
                }

                foreach ($node->props as $prop) {
                    if ($prop->name->toString() === $propertyName) {
                        return true;
                    }
                }

                return false;
            }
        );

        if (!$propertyNode instanceof Property || null === $propertyNode->type) {
            return null;
        }

        $typeNode = $propertyNode->type;

        $start = $typeNode->getAttribute('startFilePos');
        $end = $typeNode->getAttribute('endFilePos');

        if (!\is_int($start) || !\is_int($end)) {
            return null;
        }

        return mb_strcut($this->code, $start, $end - $start + 1);
    }
}
