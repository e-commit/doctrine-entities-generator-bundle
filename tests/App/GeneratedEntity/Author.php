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

#[ORM\Entity]
#[ORM\Table(name: 'author')]
class Author
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'author_id')]
    protected ?int $authorId = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $lastName = null;

    #[ORM\Embedded(class: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Address')]
    protected ?Address $address = null;

    /**
     * Not generated (public field).
     */
    #[ORM\Column(type: 'string', length: 20)]
    public ?string $phoneNumber = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Book', mappedBy: 'authors')]
    protected Collection $books;

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    /*
     * Getters / Setters (auto-generated)
     */

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function setAuthorId(?int $authorId): self
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function addBook(Book $book): self
    {
        $book->addAuthor($this);
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
        }
        $book->removeAuthor($this);

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }
}
