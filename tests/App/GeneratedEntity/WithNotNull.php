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
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Foo\Foo;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Sub\EnumInt;

#[ORM\Entity]
#[ORM\Table(name: 'with_not_null')]
class WithNotNull
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'id')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $string;

    #[ORM\Column(type: 'integer', enumType: EnumInt::class)]
    protected EnumInt $enumInt;

    #[ORM\Embedded(class: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Address')]
    protected Address $address;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Foo\Foo')]
    #[ORM\JoinColumn(name: 'foo_id', referencedColumnName: 'foo_id', nullable: true)]
    protected Foo $toOneUnidirectional;

    /*
     * Getters / Setters (auto-generated)
     */

    public function getId(): int
    {
        return $this->id;
    }

    public function setString(string $string): self
    {
        $this->string = $string;

        return $this;
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function setEnumInt(EnumInt $enumInt): self
    {
        $this->enumInt = $enumInt;

        return $this;
    }

    public function getEnumInt(): EnumInt
    {
        return $this->enumInt;
    }

    public function setToOneUnidirectional(Foo $toOneUnidirectional): self
    {
        $this->toOneUnidirectional = $toOneUnidirectional;

        return $this;
    }

    public function getToOneUnidirectional(): Foo
    {
        return $this->toOneUnidirectional;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}
