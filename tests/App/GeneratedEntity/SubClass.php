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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Foo\Bar;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Foo\Foo as MyFoo;

#[ORM\Entity]
#[ORM\Table(name: 'sub_class')]
class SubClass extends MainClass
{
    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected $nameWithoutHint;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer1', inversedBy: 'sub')]
    #[ORM\JoinColumn(name: 'first_initializer_id', referencedColumnName: 'id')]
    protected ?Initializer1 $firstInitializer = null;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer2')]
    #[ORM\JoinColumn(name: 'second_initializer_id', referencedColumnName: 'id')]
    protected ?Initializer2 $secondInitializer = null;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Foo\Foo')]
    #[ORM\JoinColumn(name: 'foo_id', referencedColumnName: 'foo_id')]
    protected ?MyFoo $foo = null;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Foo\Bar')]
    #[ORM\JoinColumn(name: 'bar_id', referencedColumnName: 'bar_id')]
    protected ?Bar $bar = null;

    #[ORM\ManyToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\SubClass', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id')]
    protected ?self $parent = null;

    /**
     * @var Collection<int, SubClass>
     */
    #[ORM\OneToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\SubClass', mappedBy: 'parent')]
    protected Collection $children;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    protected ?string $decimalField;

    #[ORM\Column(type: 'datetime')]
    protected ?\DateTime $dateField = null;

    /**
     * Custom type.
     */
    #[ORM\Column(type: 'datetime')]
    protected ?\DateTimeInterface $dateFieldWithOtherType = null;

    #[ORM\Column(type: 'boolean')]
    protected ?bool $booleanField = null;

    #[ORM\Column(type: 'text')]
    protected ?string $textField = null;

    #[ORM\Column(type: 'simple_array')]
    protected ?array $simpleArrayField = null;

    /**
     * @var ?array<string, int>
     */
    #[ORM\Column(type: 'json')]
    protected ?array $jsonField = null;

    #[ORM\Column(type: 'guid')]
    protected ?string $guidField = null;

    #[ORM\Column(type: 'my_custom_type')]
    protected mixed $customField = null;

    public function getMyFoo(): ?MyFoo
    {
        return $this->foo;
    }

    /*
     * Getters / Setters (auto-generated)
     */

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setNameWithoutHint(?string $nameWithoutHint): self
    {
        $this->nameWithoutHint = $nameWithoutHint;

        return $this;
    }

    public function getNameWithoutHint(): ?string
    {
        return $this->nameWithoutHint;
    }

    public function setDecimalField(?string $decimalField): self
    {
        $this->decimalField = $decimalField;

        return $this;
    }

    public function getDecimalField(): ?string
    {
        return $this->decimalField;
    }

    public function setDateField(?\DateTime $dateField): self
    {
        $this->dateField = $dateField;

        return $this;
    }

    public function getDateField(): ?\DateTime
    {
        return $this->dateField;
    }

    public function setDateFieldWithOtherType(?\DateTimeInterface $dateFieldWithOtherType): self
    {
        $this->dateFieldWithOtherType = $dateFieldWithOtherType;

        return $this;
    }

    public function getDateFieldWithOtherType(): ?\DateTimeInterface
    {
        return $this->dateFieldWithOtherType;
    }

    public function setBooleanField(?bool $booleanField): self
    {
        $this->booleanField = $booleanField;

        return $this;
    }

    public function getBooleanField(): ?bool
    {
        return $this->booleanField;
    }

    public function setTextField(?string $textField): self
    {
        $this->textField = $textField;

        return $this;
    }

    public function getTextField(): ?string
    {
        return $this->textField;
    }

    public function setSimpleArrayField(?array $simpleArrayField): self
    {
        $this->simpleArrayField = $simpleArrayField;

        return $this;
    }

    public function getSimpleArrayField(): ?array
    {
        return $this->simpleArrayField;
    }

    public function setJsonField(?array $jsonField): self
    {
        $this->jsonField = $jsonField;

        return $this;
    }

    public function getJsonField(): ?array
    {
        return $this->jsonField;
    }

    public function setGuidField(?string $guidField): self
    {
        $this->guidField = $guidField;

        return $this;
    }

    public function getGuidField(): ?string
    {
        return $this->guidField;
    }

    public function setCustomField(mixed $customField): self
    {
        $this->customField = $customField;

        return $this;
    }

    public function getCustomField(): mixed
    {
        return $this->customField;
    }

    public function setFirstInitializer(?Initializer1 $firstInitializer): self
    {
        $this->firstInitializer = $firstInitializer;

        return $this;
    }

    public function getFirstInitializer(): ?Initializer1
    {
        return $this->firstInitializer;
    }

    public function setSecondInitializer(?Initializer2 $secondInitializer): self
    {
        $this->secondInitializer = $secondInitializer;

        return $this;
    }

    public function getSecondInitializer(): ?Initializer2
    {
        return $this->secondInitializer;
    }

    public function setFoo(?MyFoo $foo): self
    {
        $this->foo = $foo;

        return $this;
    }

    public function getFoo(): ?MyFoo
    {
        return $this->foo;
    }

    public function setBar(?Bar $bar): self
    {
        $this->bar = $bar;

        return $this;
    }

    public function getBar(): ?Bar
    {
        return $this->bar;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function addChild(self $child): self
    {
        $child->setParent($this);
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
        }
        $child->setParent(null);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }
}
