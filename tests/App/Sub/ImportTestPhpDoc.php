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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Sub;

/**
 * @phpstan-type Person array{
 *     first_name: string,
 *     last_name: string,
 * }
 * @phpstan-type Address array{
 *     address: string,
 *     zip: string,
 *     country: string,
 * }
 */
class ImportTestPhpDoc
{
}
