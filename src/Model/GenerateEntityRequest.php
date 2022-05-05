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

class GenerateEntityRequest
{
    /**
     * @var \ReflectionClass
     */
    public $reflectionClass;

    /**
     * @var array
     */
    public $fileParts;

    /**
     * @var ClassMetadataInfo
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
     * @var array
     */
    public $newBlockContents = [];

    /**
     * @var array
     */
    public $newConstructorLines = [];

    /**
     * @var bool
     */
    public $addInitializeEntity = false;

    public function __construct(\ReflectionClass $reflectionClass, array $fileParts, ClassMetadataInfo $classMetadata, DoctrineExtractor $doctrineExtractor)
    {
        $this->reflectionClass = $reflectionClass;
        $this->fileParts = $fileParts;
        $this->classMetadata = $classMetadata;
        $this->doctrineExtractor = $doctrineExtractor;
        $this->useStatementManipulator = new UseStatementManipulator(file_get_contents($reflectionClass->getFileName()));
    }

    public function getSourceCode(): string
    {
        return $this->useStatementManipulator->getSourceCode();
    }
}
