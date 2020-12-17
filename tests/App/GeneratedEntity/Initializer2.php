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
use Ecommit\DoctrineEntitiesGeneratorBundle\Entity\EntityInitializerInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="initializer2")
 */
class Initializer2 implements EntityInitializerInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Author")
     * @ORM\JoinTable(name="initializer2_author",
     *     joinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="author_id")}
     * )
     */
    protected $authors;

    public function initializeEntity(): void
    {
        //EntityInitializerInterface is used with collection
    }

    /*
     * Getters / Setters (auto-generated)
     */

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->initializeEntity();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAuthors(): Collection
    {
        return $this->authors;
    }
}
