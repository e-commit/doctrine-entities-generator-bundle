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

#[ORM\Entity]
#[ORM\Table(name: 'bigint_table')]
class Bigint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'id')]
    protected ?int $id = null;

    /**
     * @legacy
     * DBAL 3 : ?string
     * DBAL 4 : int|string|null
     */
    #[ORM\Column(type: 'bigint')]
    protected $bigintField;

    /*
     * Getters / Setters (auto-generated)
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setBigintField(int|string|null $bigintField): self
    {
        $this->bigintField = $bigintField;

        return $this;
    }

    public function getBigintField(): int|string|null
    {
        return $this->bigintField;
    }
}
