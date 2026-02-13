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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Model;

use Doctrine\ORM\Mapping\ClassMetadata;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\Util\SourcePropertyTypeResolver;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\Util\UseStatementManipulator;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;

/**
 * @phpstan-type FileParts array{
 *      beforeBlock: ?string,
 *      startTag: string,
 *      block: ?string,
 *      endTag: string,
 *      afterBlock: ?string
 * }
 */
class GenerateEntityRequest
{
    public UseStatementManipulator $useStatementManipulator;
    public SourcePropertyTypeResolver $sourcePropertyTypeResolver;

    /**
     * @var array<string>
     */
    public array $newBlockContents = [];

    /**
     * @var array<string>
     */
    public array $newConstructorLines = [];

    public bool $addInitializeEntity = false;

    /**
     * @param \ReflectionClass<object> $reflectionClass
     * @param FileParts                $fileParts
     * @param ClassMetadata<object>    $classMetadata
     */
    public function __construct(public \ReflectionClass $reflectionClass, public array $fileParts, public ClassMetadata $classMetadata, public DoctrineExtractor $doctrineExtractor)
    {
        $sourceCode = (string) file_get_contents((string) $reflectionClass->getFileName());
        $this->useStatementManipulator = new UseStatementManipulator($sourceCode);
        $this->sourcePropertyTypeResolver = new SourcePropertyTypeResolver($sourceCode);
    }

    public function getSourceCode(): string
    {
        return $this->useStatementManipulator->getSourceCode();
    }
}
