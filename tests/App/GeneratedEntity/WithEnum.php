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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity;

use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Enum\EnumInt;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Enum\EnumString;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEnumInt(?EnumInt $enumInt): self
    {
        $this->enumInt = $enumInt;

        return $this;
    }

    public function getEnumInt(): ?EnumInt
    {
        return $this->enumInt;
    }

    public function setEnumString(?EnumString $enumString): self
    {
        $this->enumString = $enumString;

        return $this;
    }

    public function getEnumString(): ?EnumString
    {
        return $this->enumString;
    }

    public function setEnumStringSimpleArray(?array $enumStringSimpleArray): self
    {
        $this->enumStringSimpleArray = $enumStringSimpleArray;

        return $this;
    }

    public function getEnumStringSimpleArray(): ?array
    {
        return $this->enumStringSimpleArray;
    }
}
