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

class TwigHelper
{
    /**
     * @param class-string $class
     */
    public function is(?object $value, string $class): bool
    {
        return $value instanceof $class;
    }
}
