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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class GenerateEntityTemplate
{
    public function __construct(public string $template)
    {
    }
}
