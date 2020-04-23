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

/**
 * @ORM\Entity
 * @ORM\Table(name="book")
 */
class Book
{
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
     * @ORM\ManyToOne(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Category", inversedBy="books")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id")
     */
    protected $category;

    /**
     * @ORM\ManyToMany(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Author", inversedBy="books")
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

    use PriceTrait;

    /*
     * Getters / Setters (auto-generated)
     */

    public function __construct()
    {
        $this->authors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sales = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function setCategory(?\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): ?\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Category
    {
        return $this->category;
    }

    public function addAuthor(\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Author $author): self
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);
        }

        return $this;
    }

    public function getAuthors(): \Doctrine\Common\Collections\Collection
    {
        return $this->authors;
    }

    public function addSale(\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Sale $sale): self
    {
        $sale->setBook($this);
        if (!$this->sales->contains($sale)) {
            $this->sales[] = $sale;
        }

        return $this;
    }

    public function removeSale(\Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Sale $sale): self
    {
        if ($this->sales->contains($sale)) {
            $this->sales->removeElement($sale);
        }
        $sale->setBook(null);

        return $this;
    }

    public function getSales(): \Doctrine\Common\Collections\Collection
    {
        return $this->sales;
    }
}
