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

#[ORM\MappedSuperclass]
class MainClass
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'id')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $toto = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $tutu = null;

    public function getMainMethod(): string
    {
        return 'OK';
    }

    public function addValues(int $id): void
    {
        $this->id = $id;
        $this->toto = 'TOTO '.$id;
        $this->tutu = 'TUTU '.$id;
    }

    /*
     * Getters / Setters (auto-generated)
     */

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setToto(?string $toto): self
    {
        $this->toto = $toto;

        return $this;
    }

    public function getToto(): ?string
    {
        return $this->toto;
    }
}
