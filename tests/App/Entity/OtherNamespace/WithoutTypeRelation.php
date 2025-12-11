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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'without_type_relation')]
class WithoutTypeRelation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'id')]
    protected $id;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutType', mappedBy: 'relation2')]
    protected $relation2;

    /**
     * @var Collection<int, WithoutType>
     */
    #[ORM\OneToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutType', mappedBy: 'relation4')]
    protected $relation4s;

    /**
     * @var Collection<int, WithoutType>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutType', mappedBy: 'relation6s')]
    protected $relation6s;

    /*
     * Getters / Setters (auto-generated)
     */
}
