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
 * @ORM\Table(name="initializer1")
 */
class Initializer1 implements EntityInitializerInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass", mappedBy="firstInitializer")
     */
    protected $sub;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    public function initializeEntity(): void
    {
        //EntityInitializerInterface is used without collection
    }

    /*
     * Getters / Setters (auto-generated)
     */
}
