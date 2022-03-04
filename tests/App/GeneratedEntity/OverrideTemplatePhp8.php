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
use Ecommit\DoctrineEntitiesGeneratorBundle\Annotations\GenerateEntityTemplate;

/**
 * @ORM\Entity
 * @ORM\Table(name="override_template_php8")
 */
#[GenerateEntityTemplate('custom_php8.php.twig')]
class OverrideTemplatePhp8
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="author_id")
     */
    protected $Id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /*
     * Getters / Setters (auto-generated)
     */

    public function setId(?int $id): self
    {
        $this->Id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->Id;
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
}
