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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="book")
 */
class Book
{
    use PriceTrait;

    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer", name="book_id")
     */
    protected $bookId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Category", inversedBy="books")
     *
     * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id")
     */
    protected $category;

    /**
     * @ORM\ManyToMany(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Author", inversedBy="books")
     *
     * @ORM\JoinTable(name="book_author",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="author_id")}
     * )
     */
    protected $authors;

    /**
     * @ORM\OneToMany(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Sale", mappedBy="book")
     */
    protected $sales;

    /*
     * Getters / Setters (auto-generated)
     */

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->sales = new ArrayCollection();
    }

    public function getBookId(): ?int
    {
        return $this->bookId;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);
        }

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addSale(Sale $sale): self
    {
        $sale->setBook($this);
        if (!$this->sales->contains($sale)) {
            $this->sales[] = $sale;
        }

        return $this;
    }

    public function removeSale(Sale $sale): self
    {
        if ($this->sales->contains($sale)) {
            $this->sales->removeElement($sale);
        }
        $sale->setBook(null);

        return $this;
    }

    /**
     * @return Collection<int, Sale>
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }
}
