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
use Ecommit\DoctrineEntitiesGeneratorBundle\Entity\EntityInitializerInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="initializer5")
 */
class Initializer5 implements EntityInitializerInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    public function initializeEntity(): void
    {
    }

    public function __construct()
    {
        // EntityInitializerInterface is used, without collection
    }

    /*
     * Getters / Setters (auto-generated)
     */
}
