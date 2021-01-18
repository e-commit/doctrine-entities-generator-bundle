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
 * @ORM\Table(name="book")
 */
class Book
{
    use PriceTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="book_id")
     */
    protected $bookId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Category", inversedBy="books")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id")
     */
    protected $category;

    /**
     * @ORM\ManyToMany(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Author", inversedBy="books")
     * @ORM\JoinTable(name="book_author",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="author_id")}
     * )
     */
    protected $authors;

    /**
     * @ORM\OneToMany(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Sale", mappedBy="book")
     */
    protected $sales;

    /*
     * Getters / Setters (auto-generated)
     */
}
