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
}
