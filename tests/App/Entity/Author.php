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

    #[ORM\Embedded(class: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Address')]
    protected ?Address $address = null;

    /**
     * Not generated (public field).
     */
    #[ORM\Column(type: 'string', length: 20)]
    public ?string $phoneNumber = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\ManyToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Book', mappedBy: 'authors')]
    protected Collection $books;

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    /*
     * Getters / Setters (auto-generated)
     */
}
