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
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Bar;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Foo as MyFoo;

#[ORM\Entity]
#[ORM\Table(name: 'sub_class')]
class SubClass extends MainClass
{
    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected $nameWithoutHint;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer1', inversedBy: 'sub')]
    #[ORM\JoinColumn(name: 'first_initializer_id', referencedColumnName: 'id')]
    protected ?Initializer1 $firstInitializer = null;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer2')]
    #[ORM\JoinColumn(name: 'second_initializer_id', referencedColumnName: 'id')]
    protected ?Initializer2 $secondInitializer = null;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Foo')]
    #[ORM\JoinColumn(name: 'foo_id', referencedColumnName: 'foo_id')]
    protected ?MyFoo $foo = null;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Bar')]
    #[ORM\JoinColumn(name: 'bar_id', referencedColumnName: 'bar_id')]
    protected ?Bar $bar = null;

    #[ORM\ManyToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id')]
    protected ?self $parent = null;

    /**
     * @var Collection<int, SubClass>
     */
    #[ORM\OneToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass', mappedBy: 'parent')]
    protected Collection $children;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    protected ?string $decimalField;

    #[ORM\Column(type: 'datetime')]
    protected ?\DateTime $dateField = null;

    /**
     * Custom type.
     */
    #[ORM\Column(type: 'datetime')]
    protected ?\DateTimeInterface $dateFieldWithOtherType = null;

    #[ORM\Column(type: 'boolean')]
    protected ?bool $booleanField = null;

    #[ORM\Column(type: 'text')]
    protected ?string $textField = null;

    #[ORM\Column(type: 'simple_array')]
    protected ?array $simpleArrayField = null;

    /**
     * @var ?array<string, int>
     */
    #[ORM\Column(type: 'json')]
    protected ?array $jsonField = null;

    #[ORM\Column(type: 'guid')]
    protected ?string $guidField = null;

    #[ORM\Column(type: 'my_custom_type')]
    protected mixed $customField = null;

    public function getMyFoo(): ?MyFoo
    {
        return $this->foo;
    }

    /*
     * Getters / Setters (auto-generated)
     */
}
