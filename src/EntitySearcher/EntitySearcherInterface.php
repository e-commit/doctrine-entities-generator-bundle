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

use Doctrine\Persistence\Mapping\ClassMetadata;

interface EntitySearcherInterface
{
    /**
     * @return array<class-string>
     */
    public function search(string $input): array;

    /**
     * @param ClassMetadata<object> $metadata
     */
    public function classCanBeGenerated(ClassMetadata $metadata): bool;
}
