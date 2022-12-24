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

use PhpParser\Builder;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

/**
 * This class is extracted (with adaptations) from ClassSourceManipulator and Str classes from symfony/maker-bundle.
 * The original classes are marked as internal and therefore BC can be broken at any time.
 * https://github.com/symfony/maker-bundle
 * (c) Fabien Potencier <fabien@symfony.com>.
 */
class UseStatementManipulator
{
    /**
     * @var Parser\Php7
     */
    protected $parser;

    /**
     * @var Lexer\Emulative
     */
    protected $lexer;

    /**
     * @var Standard
     */
    protected $printer;

    /**
     * @var string
     */
    protected $sourceCode;

    /**
     * @var array<Node\Stmt>|null
     */
    protected $oldStmts;

    /**
     * @var array<mixed>
     */
    protected $oldTokens;

    /**
     * @var array<Node>|null
     */
    protected $newStmts;

    public function __construct(string $sourceCode)
    {
        $this->lexer = new Lexer\Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
        $this->parser = new Parser\Php7($this->lexer);
        $this->printer = new Standard();

        $this->setSourceCode($sourceCode);
    }

    public function getSourceCode(): string
    {
        return $this->sourceCode;
    }

    protected function setSourceCode(string $sourceCode): void
    {
        $this->sourceCode = $sourceCode;
        $this->oldStmts = $this->parser->parse($sourceCode);
        $this->oldTokens = $this->lexer->getTokens();
        if (null === $this->oldStmts) {
            return;
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NodeVisitor\CloningVisitor());
        $traverser->addVisitor(new NodeVisitor\NameResolver(null, [
            'replaceNodes' => false,
        ]));
        $this->newStmts = $traverser->traverse($this->oldStmts);
    }

    public function addUseStatementIfNecessary(string $class): string
    {
        $shortClassName = self::getShortClassName($class);
        if ($this->isInSameNamespace($class)) {
            return $shortClassName;
        }

        $namespaceNode = $this->getNamespaceNode();

        $targetIndex = null;
        $addLineBreak = false;
        $lastUseStmtIndex = null;
        /**
         * @var int       $index
         * @var Node\Stmt $stmt
         */
        foreach ($namespaceNode->stmts as $index => $stmt) {
            if ($stmt instanceof Node\Stmt\Use_) {
                // I believe this is an array to account for use statements with {}
                foreach ($stmt->uses as $use) {
                    $alias = $use->alias ? $use->alias->name : $use->name->getLast();

                    // the use statement already exists? Don't add it again
                    if ($class === (string) $use->name) {
                        return $alias;
                    }

                    if ($alias === $shortClassName) {
                        // we have a conflicting alias!
                        // to be safe, use the fully-qualified class name
                        // everywhere and do not add another use statement
                        return '\\'.$class;
                    }
                }

                // if $class is alphabetically before this use statement, place it before
                // only set $targetIndex the first time you find it
                if (null === $targetIndex && self::areClassesAlphabetical($class, (string) $stmt->uses[0]->name)) {
                    $targetIndex = $index;
                }

                $lastUseStmtIndex = $index;
            } elseif ($stmt instanceof Node\Stmt\Class_) {
                if (null !== $targetIndex) {
                    // we already found where to place the use statement

                    break;
                }

                // we hit the class! If there were any use statements,
                // then put this at the bottom of the use statement list
                if (null !== $lastUseStmtIndex) {
                    $targetIndex = $lastUseStmtIndex + 1;
                } else {
                    $targetIndex = $index;
                    $addLineBreak = true;
                }

                break;
            }
        }

        if (null === $targetIndex) {
            throw new \Exception('Could not find a class!');
        }

        $newUseNode = (new Builder\Use_($class, Node\Stmt\Use_::TYPE_NORMAL))->getNode();
        array_splice(
            $namespaceNode->stmts,
            $targetIndex,
            0,
            $addLineBreak ? [$newUseNode, $this->createBlankLineNode()] : [$newUseNode]
        );

        $this->updateSourceCodeFromNewStmts();

        return $shortClassName;
    }

    protected function updateSourceCodeFromNewStmts(): void
    {
        if (null === $this->newStmts || null === $this->oldStmts) {
            return;
        }

        $newCode = $this->printer->printFormatPreserving(
            $this->newStmts,
            $this->oldStmts,
            $this->oldTokens
        );

        // replace the "fake" item that may be in the code (allowing for different indentation)
        $newCode = preg_replace('/use __EXTRA__LINE;/', '', $newCode);

        $this->setSourceCode((string) $newCode);
    }

    protected function getNamespaceNode(): Node\Stmt\Namespace_
    {
        $node = $this->findFirstNode(/** @param mixed $node */ function ($node) {
            return $node instanceof Node\Stmt\Namespace_;
        });

        if (!$node || !$node instanceof Node\Stmt\Namespace_) {
            throw new \Exception('Could not find namespace node');
        }

        return $node;
    }

    /**
     * @return Node|null
     */
    protected function findFirstNode(callable $filterCallback)
    {
        if (null === $this->newStmts) {
            return null;
        }
        $traverser = new NodeTraverser();
        $visitor = new NodeVisitor\FirstFindingVisitor($filterCallback);
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->newStmts);

        return $visitor->getFoundNode();
    }

    protected function isInSameNamespace(string $class): bool
    {
        $currentNamespace = $this->getNamespaceNode()->name;
        $namespace = self::getNamespace($class);
        if (null === $currentNamespace) {
            return '' === $namespace;
        }

        return $currentNamespace->toCodeString() === $namespace;
    }

    private function createBlankLineNode(): Node
    {
        return (new Builder\Use_('__EXTRA__LINE', Node\Stmt\Use_::TYPE_NORMAL))
            ->getNode()
        ;
    }

    public static function getNamespace(string $fullClassName): string
    {
        $length = mb_strrpos($fullClassName, '\\');
        if (false === $length) {
            return '';
        }

        return mb_substr($fullClassName, 0, $length);
    }

    public static function getShortClassName(string $fullClassName): string
    {
        if (empty(self::getNamespace($fullClassName))) {
            return $fullClassName;
        }

        $lastSlashPosition = mb_strrpos($fullClassName, '\\');
        if (false === $lastSlashPosition) {
            return $fullClassName;
        }

        return mb_substr($fullClassName, $lastSlashPosition + 1);
    }

    public static function areClassesAlphabetical(string $class1, string $class2): bool
    {
        $arr1 = [$class1, $class2];
        $arr2 = [$class1, $class2];
        sort($arr2);

        return $arr1[0] == $arr2[0];
    }
}
