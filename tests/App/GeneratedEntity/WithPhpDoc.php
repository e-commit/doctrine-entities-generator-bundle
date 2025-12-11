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
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Sub\ImportTestPhpDoc;

/**
 * @phpstan-type MyType positive-int
 * @phpstan-type MyArrayShape array{
 *     first_name: string,
 *     last_name: string,
 *     address?: MyAddress,
 * }
 *
 * @phpstan-import-type Person from ImportTestPhpDoc
 * @phpstan-import-type Address from ImportTestPhpDoc as MyAddress
 */
#[ORM\Entity]
#[ORM\Table(name: 'with_phpdoc')]
class WithPhpDoc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $withoutPhpDoc;

    /**
     * @var positive-int
     */
    #[ORM\Column(type: 'integer')]
    protected int $withPositiveIntPhpDoc;

    /**
     * @var int<0, 10>|int<100, 110>|null
     */
    #[ORM\Column(type: 'integer')]
    protected ?int $withMultipleTypesPhpDoc;

    /** @var positive-int */
    #[ORM\Column(type: 'integer')]
    protected int $withInlinePhpDoc;

    /**
     * My comment.
     *
     * @var positive-int
     */
    #[ORM\Column(type: 'integer')]
    protected int $withCommentPhpDoc;

    /**
     * My comment.
     */
    #[ORM\Column(type: 'integer')]
    protected int $withOnlyCommentPhpDoc;

    /**
     * @var MyType
     */
    #[ORM\Column(type: 'integer')]
    protected int $withPhpstanTypePhpDoc;

    /**
     * @var Person
     */
    #[ORM\Column(type: 'json')]
    protected array $withPhpstanImportTypePhpDoc;

    /**
     * @var MyAddress
     */
    #[ORM\Column(type: 'json')]
    protected array $withPhpstanImportTypeAliasPhpDoc;

    /**
     * @var array{
     *     first_name: string,
     *     last_name: string,
     *     address?: MyAddress,
     * }
     */
    #[ORM\Column(type: 'json')]
    protected array $withArrayShapePhpDoc;

    /**
     * @var MyArrayShape
     */
    #[ORM\Column(type: 'json')]
    protected array $withArrayShapeTypePhpDoc;

    /*
     * Getters / Setters (auto-generated)
     */

    public function getWithoutPhpDoc(): int
    {
        return $this->withoutPhpDoc;
    }

    /**
     * @param positive-int $withPositiveIntPhpDoc
     */
    public function setWithPositiveIntPhpDoc(int $withPositiveIntPhpDoc): self
    {
        $this->withPositiveIntPhpDoc = $withPositiveIntPhpDoc;

        return $this;
    }

    /**
     * @return positive-int
     */
    public function getWithPositiveIntPhpDoc(): int
    {
        return $this->withPositiveIntPhpDoc;
    }

    /**
     * @param (int<0, 10> | int<100, 110> | null) $withMultipleTypesPhpDoc
     */
    public function setWithMultipleTypesPhpDoc(?int $withMultipleTypesPhpDoc): self
    {
        $this->withMultipleTypesPhpDoc = $withMultipleTypesPhpDoc;

        return $this;
    }

    /**
     * @return (int<0, 10> | int<100, 110> | null)
     */
    public function getWithMultipleTypesPhpDoc(): ?int
    {
        return $this->withMultipleTypesPhpDoc;
    }

    /**
     * @param positive-int $withInlinePhpDoc
     */
    public function setWithInlinePhpDoc(int $withInlinePhpDoc): self
    {
        $this->withInlinePhpDoc = $withInlinePhpDoc;

        return $this;
    }

    /**
     * @return positive-int
     */
    public function getWithInlinePhpDoc(): int
    {
        return $this->withInlinePhpDoc;
    }

    /**
     * @param positive-int $withCommentPhpDoc
     */
    public function setWithCommentPhpDoc(int $withCommentPhpDoc): self
    {
        $this->withCommentPhpDoc = $withCommentPhpDoc;

        return $this;
    }

    /**
     * @return positive-int
     */
    public function getWithCommentPhpDoc(): int
    {
        return $this->withCommentPhpDoc;
    }

    public function setWithOnlyCommentPhpDoc(int $withOnlyCommentPhpDoc): self
    {
        $this->withOnlyCommentPhpDoc = $withOnlyCommentPhpDoc;

        return $this;
    }

    public function getWithOnlyCommentPhpDoc(): int
    {
        return $this->withOnlyCommentPhpDoc;
    }

    /**
     * @param MyType $withPhpstanTypePhpDoc
     */
    public function setWithPhpstanTypePhpDoc(int $withPhpstanTypePhpDoc): self
    {
        $this->withPhpstanTypePhpDoc = $withPhpstanTypePhpDoc;

        return $this;
    }

    /**
     * @return MyType
     */
    public function getWithPhpstanTypePhpDoc(): int
    {
        return $this->withPhpstanTypePhpDoc;
    }

    /**
     * @param Person $withPhpstanImportTypePhpDoc
     */
    public function setWithPhpstanImportTypePhpDoc(array $withPhpstanImportTypePhpDoc): self
    {
        $this->withPhpstanImportTypePhpDoc = $withPhpstanImportTypePhpDoc;

        return $this;
    }

    /**
     * @return Person
     */
    public function getWithPhpstanImportTypePhpDoc(): array
    {
        return $this->withPhpstanImportTypePhpDoc;
    }

    /**
     * @param MyAddress $withPhpstanImportTypeAliasPhpDoc
     */
    public function setWithPhpstanImportTypeAliasPhpDoc(array $withPhpstanImportTypeAliasPhpDoc): self
    {
        $this->withPhpstanImportTypeAliasPhpDoc = $withPhpstanImportTypeAliasPhpDoc;

        return $this;
    }

    /**
     * @return MyAddress
     */
    public function getWithPhpstanImportTypeAliasPhpDoc(): array
    {
        return $this->withPhpstanImportTypeAliasPhpDoc;
    }

    /**
     * @param array{first_name: string, last_name: string, address?: MyAddress} $withArrayShapePhpDoc
     */
    public function setWithArrayShapePhpDoc(array $withArrayShapePhpDoc): self
    {
        $this->withArrayShapePhpDoc = $withArrayShapePhpDoc;

        return $this;
    }

    /**
     * @return array{first_name: string, last_name: string, address?: MyAddress}
     */
    public function getWithArrayShapePhpDoc(): array
    {
        return $this->withArrayShapePhpDoc;
    }

    /**
     * @param MyArrayShape $withArrayShapeTypePhpDoc
     */
    public function setWithArrayShapeTypePhpDoc(array $withArrayShapeTypePhpDoc): self
    {
        $this->withArrayShapeTypePhpDoc = $withArrayShapeTypePhpDoc;

        return $this;
    }

    /**
     * @return MyArrayShape
     */
    public function getWithArrayShapeTypePhpDoc(): array
    {
        return $this->withArrayShapeTypePhpDoc;
    }
}
