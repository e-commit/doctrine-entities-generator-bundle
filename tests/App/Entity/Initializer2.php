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

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Entity\EntityInitializerInterface;

#[ORM\Entity]
#[ORM\Table(name: 'initializer2')]
class Initializer2 implements EntityInitializerInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'id')]
    protected ?int $id = null;

    /**
     * @var Collection<int, Author>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Author')]
    #[ORM\JoinTable(name: 'initializer2_author')]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'author_id', referencedColumnName: 'author_id')]
    protected Collection $authors;

    public function initializeEntity(): void
    {
        // EntityInitializerInterface is used with collection
    }

    /*
     * Getters / Setters (auto-generated)
     */
}
