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
 * @ORM\Table(name="initializer3")
 */
class Initializer3
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Author")
     * @ORM\JoinTable(name="initializer3_author",
     *     joinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="author_id")}
     * )
     */
    protected $authors;

    /*
     * Getters / Setters (auto-generated)
     */

    public function __construct()
    {
        $this->authors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
}
