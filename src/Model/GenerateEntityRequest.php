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

use Doctrine\ORM\Mapping\ClassMetadataInfo;
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
    /**
     * @var \ReflectionClass<object>
     */
    public $reflectionClass;

    /**
     * @var FileParts
     */
    public $fileParts;

    /**
     * @var ClassMetadataInfo<object>
     */
    public $classMetadata;

    /**
     * @var DoctrineExtractor
     */
    public $doctrineExtractor;

    /**
     * @var UseStatementManipulator
     */
    public $useStatementManipulator;

    /**
     * @var array<string>
     */
    public $newBlockContents = [];

    /**
     * @var array<string>
     */
    public $newConstructorLines = [];

    /**
     * @var bool
     */
    public $addInitializeEntity = false;

    /**
     * @param \ReflectionClass<object>  $reflectionClass
     * @param FileParts                 $fileParts
     * @param ClassMetadataInfo<object> $classMetadata
     */
    public function __construct(\ReflectionClass $reflectionClass, array $fileParts, ClassMetadataInfo $classMetadata, DoctrineExtractor $doctrineExtractor)
    {
        $this->reflectionClass = $reflectionClass;
        $this->fileParts = $fileParts;
        $this->classMetadata = $classMetadata;
        $this->doctrineExtractor = $doctrineExtractor;
        $this->useStatementManipulator = new UseStatementManipulator((string) file_get_contents((string) $reflectionClass->getFileName()));
    }

    public function getSourceCode(): string
    {
        return $this->useStatementManipulator->getSourceCode();
    }
}
