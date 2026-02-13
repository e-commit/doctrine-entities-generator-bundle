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

#[ORM\Entity]
#[ORM\Table(name: 'without_type_relation')]
class WithoutTypeRelation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'id')]
    protected $id;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace\WithoutType', mappedBy: 'relation2')]
    protected $relation2;

    #[ORM\OneToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace\WithoutType', mappedBy: 'relation4')]
    protected $relation4s;

    /**
     * @var Collection<int, WithoutType>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\OtherNamespace\WithoutType', mappedBy: 'relation6s')]
    protected $relation6s;

    /*
     * Getters / Setters (auto-generated)
     */

    public function __construct()
    {
        $this->relation4s = new ArrayCollection();
        $this->relation6s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setRelation2(?WithoutType $relation2): self
    {
        if (null === $relation2 && null !== $this->relation2) {
            $this->relation2->setRelation2(null);
        } elseif (null !== $relation2) {
            $relation2->setRelation2($this);
        }
        $this->relation2 = $relation2;

        return $this;
    }

    public function getRelation2(): ?WithoutType
    {
        return $this->relation2;
    }

    public function addRelation4(WithoutType $relation4): self
    {
        $relation4->setRelation4($this);
        if (!$this->relation4s->contains($relation4)) {
            $this->relation4s[] = $relation4;
        }

        return $this;
    }

    public function removeRelation4(WithoutType $relation4): self
    {
        if ($this->relation4s->contains($relation4)) {
            $this->relation4s->removeElement($relation4);
        }
        $relation4->setRelation4(null);

        return $this;
    }

    /**
     * @return Collection<int, WithoutType>
     */
    public function getRelation4s(): Collection
    {
        return $this->relation4s;
    }

    public function addRelation6(WithoutType $relation6): self
    {
        $relation6->addRelation6($this);
        if (!$this->relation6s->contains($relation6)) {
            $this->relation6s[] = $relation6;
        }

        return $this;
    }

    public function removeRelation6(WithoutType $relation6): self
    {
        if ($this->relation6s->contains($relation6)) {
            $this->relation6s->removeElement($relation6);
        }
        $relation6->removeRelation6($this);

        return $this;
    }

    /**
     * @return Collection<int, WithoutType>
     */
    public function getRelation6s(): Collection
    {
        return $this->relation6s;
    }
}
