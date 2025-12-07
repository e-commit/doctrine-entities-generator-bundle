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
use Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\GenerateEntityTemplate;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[GenerateEntityTemplate('custom_end_tag.php.twig')]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'category_id')]
    protected ?int $categoryId = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $name = null;

    #[ORM\Column(type: 'my_custom_type')]
    protected ?string $customField = null;

    #[ORM\Column(type: 'my_custom_type')]
    protected $customFieldWithoutHint;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Book', mappedBy: 'category')]
    protected Collection $books;

    public function methodBeforeBlock(): string
    {
        return 'OK';
    }

    /*
     * Getters / Setters (auto-generated)
     */

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function setCategoryId(?int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setCustomField(?string $customField): self
    {
        $this->customField = $customField;

        return $this;
    }

    public function getCustomField(): ?string
    {
        return $this->customField;
    }

    public function setCustomFieldWithoutHint(?string $customFieldWithoutHint): self
    {
        $this->customFieldWithoutHint = $customFieldWithoutHint;

        return $this;
    }

    public function getCustomFieldWithoutHint(): ?string
    {
        return $this->customFieldWithoutHint;
    }

    public function addBook(Book $book): self
    {
        $book->setCategory($this);
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
        $book->setCategory(null);

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /*
     * End Getters / Setters (auto-generated)
     */

    public function methodAfterBlock(): string
    {
        return 'OK';
    }
}
