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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Sub\EnumInt;

#[ORM\Entity]
#[ORM\Table(name: 'without_type')]
class WithoutType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'id')]
    protected $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected $string;

    #[ORM\Column(type: 'integer', enumType: EnumInt::class)]
    protected $enumInt;

    #[ORM\Embedded(class: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Address')]
    protected $address;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutTypeRelation')]
    #[ORM\JoinColumn(name: 'relation1_id', referencedColumnName: 'id', nullable: true)]
    protected $relation1;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutTypeRelation', inversedBy: 'relation2')]
    #[ORM\JoinColumn(name: 'relation2_id', referencedColumnName: 'id', nullable: true)]
    protected $relation2;

    #[ORM\ManyToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutTypeRelation')]
    #[ORM\JoinColumn(name: 'relation3_id', referencedColumnName: 'id')]
    protected $relation3;

    #[ORM\ManyToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutTypeRelation', inversedBy: 'relation4s')]
    #[ORM\JoinColumn(name: 'relation4_id', referencedColumnName: 'id')]
    protected $relation4;

    /**
     * @var Collection<int, WithoutTypeRelation>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutTypeRelation')]
    #[ORM\JoinTable(name: 'without_type_relation5')]
    #[ORM\JoinColumn(name: 'owning_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'reverse_id', referencedColumnName: 'id')]
    protected $relation5s;

    /**
     * @var Collection<int, WithoutTypeRelation>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutTypeRelation', inversedBy: 'relation6s')]
    #[ORM\JoinTable(name: 'without_type_relation6')]
    #[ORM\JoinColumn(name: 'owning_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'reverse_id', referencedColumnName: 'id')]
    protected $relation6s;

    /*
     * Getters / Setters (auto-generated)
     */
}
