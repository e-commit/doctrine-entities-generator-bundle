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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Address;
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

    #[ORM\Embedded(class: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Address')]
    protected $address;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace\WithoutTypeRelation')]
    #[ORM\JoinColumn(name: 'relation1_id', referencedColumnName: 'id', nullable: true)]
    protected $relation1;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace\WithoutTypeRelation', inversedBy: 'relation2')]
    #[ORM\JoinColumn(name: 'relation2_id', referencedColumnName: 'id', nullable: true)]
    protected $relation2;

    #[ORM\ManyToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace\WithoutTypeRelation')]
    #[ORM\JoinColumn(name: 'relation3_id', referencedColumnName: 'id')]
    protected $relation3;

    #[ORM\ManyToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace\WithoutTypeRelation', inversedBy: 'relation4s')]
    #[ORM\JoinColumn(name: 'relation4_id', referencedColumnName: 'id')]
    protected $relation4;

    /**
     * @var Collection<int, WithoutTypeRelation>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace\WithoutTypeRelation')]
    #[ORM\JoinTable(name: 'without_type_relation5')]
    #[ORM\JoinColumn(name: 'owning_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'reverse_id', referencedColumnName: 'id')]
    protected $relation5s;

    /**
     * @var Collection<int, WithoutTypeRelation>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace\WithoutTypeRelation', inversedBy: 'relation6s')]
    #[ORM\JoinTable(name: 'without_type_relation6')]
    #[ORM\JoinColumn(name: 'owning_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'reverse_id', referencedColumnName: 'id')]
    protected $relation6s;

    /*
     * Getters / Setters (auto-generated)
     */

    public function __construct()
    {
        $this->relation5s = new ArrayCollection();
        $this->relation6s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setString(?string $string): self
    {
        $this->string = $string;

        return $this;
    }

    public function getString(): ?string
    {
        return $this->string;
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

    public function setRelation1(?WithoutTypeRelation $relation1): self
    {
        $this->relation1 = $relation1;

        return $this;
    }

    public function getRelation1(): ?WithoutTypeRelation
    {
        return $this->relation1;
    }

    public function setRelation2(?WithoutTypeRelation $relation2): self
    {
        $this->relation2 = $relation2;

        return $this;
    }

    public function getRelation2(): ?WithoutTypeRelation
    {
        return $this->relation2;
    }

    public function setRelation3(?WithoutTypeRelation $relation3): self
    {
        $this->relation3 = $relation3;

        return $this;
    }

    public function getRelation3(): ?WithoutTypeRelation
    {
        return $this->relation3;
    }

    public function setRelation4(?WithoutTypeRelation $relation4): self
    {
        $this->relation4 = $relation4;

        return $this;
    }

    public function getRelation4(): ?WithoutTypeRelation
    {
        return $this->relation4;
    }

    public function addRelation5(WithoutTypeRelation $relation5): self
    {
        if (!$this->relation5s->contains($relation5)) {
            $this->relation5s[] = $relation5;
        }

        return $this;
    }

    public function removeRelation5(WithoutTypeRelation $relation5): self
    {
        if ($this->relation5s->contains($relation5)) {
            $this->relation5s->removeElement($relation5);
        }

        return $this;
    }

    /**
     * @return Collection<int, WithoutTypeRelation>
     */
    public function getRelation5s(): Collection
    {
        return $this->relation5s;
    }

    public function addRelation6(WithoutTypeRelation $relation6): self
    {
        if (!$this->relation6s->contains($relation6)) {
            $this->relation6s[] = $relation6;
        }

        return $this;
    }

    public function removeRelation6(WithoutTypeRelation $relation6): self
    {
        if ($this->relation6s->contains($relation6)) {
            $this->relation6s->removeElement($relation6);
        }

        return $this;
    }

    /**
     * @return Collection<int, WithoutTypeRelation>
     */
    public function getRelation6s(): Collection
    {
        return $this->relation6s;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }
}
