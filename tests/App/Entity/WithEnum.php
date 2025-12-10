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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Sub\EnumInt;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Sub\EnumString;

#[ORM\Entity]
#[ORM\Table(name: 'with_enum')]
class WithEnum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'id')]
    protected ?int $id = null;

    #[ORM\Column(type: 'integer', enumType: EnumInt::class)]
    protected ?EnumInt $enumInt = null;

    #[ORM\Column(type: 'string', enumType: EnumString::class)]
    protected ?EnumString $enumString = null;

    #[ORM\Column(type: 'simple_array', enumType: EnumString::class)]
    protected ?array $enumStringSimpleArray = null;

    /*
     * Getters / Setters (auto-generated)
     */
}
