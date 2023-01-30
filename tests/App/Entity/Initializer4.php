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

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="initializer4")
 */
class Initializer4
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Author")
     *
     * @ORM\JoinTable(name="initializer4_author",
     *     joinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="author_id")}
     * )
     */
    protected $authors;

    public function __construct()
    {
        // EntityInitializerInterface is not used, with collection
    }

    /*
     * Getters / Setters (auto-generated)
     */
}
