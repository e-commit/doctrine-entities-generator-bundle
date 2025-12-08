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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\IgnoreGenerateEntity;

class EntitySearcher implements EntitySearcherInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function search(string $input): array
    {
        $classes = [];
        foreach ($this->registry->getManagers() as $manager) {
            $classes = array_merge(
                $classes,
                $this->searchInManager($manager, $input)
            );
        }

        sort($classes);

        return $classes;
    }

    /**
     * @return array<class-string>
     */
    protected function searchInManager(ObjectManager $manager, string $input): array
    {
        $metadataFactory = $manager->getMetadataFactory();

        $classes = [];
        foreach ($metadataFactory->getAllMetadata() as $metadata) {
            $className = $metadata->getReflectionClass()->getName();
            if ($this->inputMatchesClass($className, $input) && $this->classCanBeGenerated($metadata)) {
                $classes[] = $className;
            }
            if ($metadata instanceof ClassMetadataInfo) {
                foreach ($metadata->embeddedClasses as $embedded) {
                    $embeddedMetadata = $manager->getClassMetadata($embedded['class']);
                    if ($this->inputMatchesClass($embedded['class'], $input) && $this->classCanBeGenerated($embeddedMetadata)) {
                        $classes[] = $embedded['class'];
                    }
                }
            }
        }

        return $classes;
    }

    protected function inputMatchesClass(string $className, string $input): bool
    {
        $input = str_replace('/', '\\', $input);

        if (preg_match('/\*/', $input)) {
            $input = preg_quote($input);
            $pattern = '/^'.str_replace('\*', '.+', $input).'$/';

            return 1 === preg_match($pattern, $className);
        }

        return $input === $className;
    }

    public function classCanBeGenerated(ClassMetadata $metadata): bool
    {
        if (!($metadata instanceof \Doctrine\ORM\Mapping\ClassMetadata)) {
            return false;
        }

        $reflectionClass = $metadata->getReflectionClass();
        if ($reflectionClass->isInterface() || $reflectionClass->isTrait()) {
            return false;
        }

        if (\count($reflectionClass->getAttributes(IgnoreGenerateEntity::class)) > 0) {
            return false;
        }

        return true;
    }
}
