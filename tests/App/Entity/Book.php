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

#[ORM\Entity]
#[ORM\Table(name: 'book')]
class Book
{
    use PriceTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', name: 'book_id')]
    protected ?int $bookId = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $title = null;

    #[ORM\ManyToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Category', inversedBy: 'books')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'category_id')]
    protected ?Category $category = null;

    /**
     * @var Collection<int, Author>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Author', inversedBy: 'books')]
    #[ORM\JoinTable(name: 'book_author')]
    #[ORM\JoinColumn(name: 'book_id', referencedColumnName: 'book_id')]
    #[ORM\InverseJoinColumn(name: 'author_id', referencedColumnName: 'author_id')]
    protected Collection $authors;

    /**
     * @var Collection<int, Sale>
     */
    #[ORM\OneToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Sale', mappedBy: 'book')]
    protected Collection $sales;

    /*
     * Getters / Setters (auto-generated)
     */
}
