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

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="sub_class")
 */
class SubClass extends MainClass
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\OneToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer1", inversedBy="sub")
     *
     * @ORM\JoinColumn(name="first_initializer_id", referencedColumnName="id")
     */
    protected $firstInitializer;

    /**
     * @ORM\OneToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer2")
     *
     * @ORM\JoinColumn(name="second_initializer_id", referencedColumnName="id")
     */
    protected $secondInitializer;

    /**
     * @ORM\OneToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Foo\Foo")
     *
     * @ORM\JoinColumn(name="foo_id", referencedColumnName="foo_id")
     */
    protected $foo;

    /**
     * @ORM\OneToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Foo\Bar")
     *
     * @ORM\JoinColumn(name="bar_id", referencedColumnName="bar_id")
     */
    protected $bar;

    /**
     * @ORM\ManyToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\SubClass", inversedBy="children")
     *
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\SubClass", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    protected $decimalField;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    protected ?string $decimalFieldWithHint;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateField;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $booleanField;

    /**
     * @ORM\Column(type="text")
     */
    protected $textField;

    /**
     * @ORM\Column(type="object")
     */
    protected $objectField;

    /**
     * @ORM\Column(type="array")
     */
    protected $arrayField;

    /**
     * @ORM\Column(type="simple_array")
     */
    protected $simpleArrayField;

    /**
     * @ORM\Column(type="json")
     */
    protected $jsonField;

    /**
     * @ORM\Column(type="guid")
     */
    protected $guidField;

    /**
     * @ORM\Column(type="my_custom_type")
     *
     * Type not defined in template
     */
    protected $customField;

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

    public function setDecimalField($decimalField): self
    {
        $this->decimalField = $decimalField;

        return $this;
    }

    public function getDecimalField()
    {
        return $this->decimalField;
    }

    public function setDecimalFieldWithHint(?string $decimalFieldWithHint): self
    {
        $this->decimalFieldWithHint = $decimalFieldWithHint;

        return $this;
    }

    public function getDecimalFieldWithHint(): ?string
    {
        return $this->decimalFieldWithHint;
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

    public function setObjectField(?object $objectField): self
    {
        $this->objectField = $objectField;

        return $this;
    }

    public function getObjectField(): ?object
    {
        return $this->objectField;
    }

    public function setArrayField(?array $arrayField): self
    {
        $this->arrayField = $arrayField;

        return $this;
    }

    public function getArrayField(): ?array
    {
        return $this->arrayField;
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

    public function setJsonField($jsonField): self
    {
        $this->jsonField = $jsonField;

        return $this;
    }

    public function getJsonField()
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

    public function setCustomField($customField): self
    {
        $this->customField = $customField;

        return $this;
    }

    public function getCustomField()
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
