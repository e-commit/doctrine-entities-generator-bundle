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

#[ORM\Entity]
#[ORM\Table(name: 'sale')]
class Sale
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Book', inversedBy: 'sales')]
    #[ORM\JoinColumn(name: 'book_id', referencedColumnName: 'book_id', nullable: false)]
    protected $book;

    #[ORM\Id]
    #[ORM\Column(type: 'smallint')]
    protected $year;

    #[ORM\Column(type: 'integer')]
    protected $countSales;

    /*
     * Getters / Setters (auto-generated)
     */
}
