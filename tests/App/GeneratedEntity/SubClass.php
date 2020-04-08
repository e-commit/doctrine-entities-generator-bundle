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

/**
 * @ORM\Entity
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
     * @ORM\JoinColumn(name="first_initializer_id", referencedColumnName="id")
     */
    protected $firstInitializer;

    /**
     * @ORM\OneToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer2")
     * @ORM\JoinColumn(name="second_initializer_id", referencedColumnName="id")
     */
    protected $secondInitializer;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    protected $decimalField;

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

    /*
     * Getters / Setters (auto-generated)
     */

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

    public function setFirstInitializer(\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer1 $firstInitializer = null): self
    {
        $this->firstInitializer = $firstInitializer;

        return $this;
    }

    public function getFirstInitializer(): ?\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer1
    {
        return $this->firstInitializer;
    }

    public function setSecondInitializer(\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer2 $secondInitializer = null): self
    {
        $this->secondInitializer = $secondInitializer;

        return $this;
    }

    public function getSecondInitializer(): ?\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer2
    {
        return $this->secondInitializer;
    }
}
