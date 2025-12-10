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
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Foo;
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

    #[ORM\Embedded(class: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Address')]
    protected Address $address;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Foo')]
    #[ORM\JoinColumn(name: 'foo_id', referencedColumnName: 'foo_id', nullable: true)]
    protected Foo $toOneUnidirectional;

    /*
     * Getters / Setters (auto-generated)
     */
}
