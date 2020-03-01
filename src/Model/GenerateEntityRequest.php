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
     * @var ClassMetadata
     */
    public $classMetadata;

    /**
     * @var DoctrineExtractor
     */
    public $doctrineExtractor;

    public $newBlockContents = [];

    public $newConstructorLines = [];

    public $addInitializeEntity = false;

    public function __construct(\ReflectionClass $reflectionClass, array $fileParts, ClassMetadata $classMetadata, DoctrineExtractor $doctrineExtractor)
    {
        $this->reflectionClass = $reflectionClass;
        $this->fileParts = $fileParts;
        $this->classMetadata = $classMetadata;
        $this->doctrineExtractor = $doctrineExtractor;
    }
}
