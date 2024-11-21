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
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Foo as MyFoo;

#[ORM\Entity]
#[ORM\Table(name: 'sub_class')]
class SubClass extends MainClass
{
    #[ORM\Column(type: 'string', length: 255)]
    protected $name;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer1', inversedBy: 'sub')]
    #[ORM\JoinColumn(name: 'first_initializer_id', referencedColumnName: 'id')]
    protected $firstInitializer;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer2')]
    #[ORM\JoinColumn(name: 'second_initializer_id', referencedColumnName: 'id')]
    protected $secondInitializer;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Foo')]
    #[ORM\JoinColumn(name: 'foo_id', referencedColumnName: 'foo_id')]
    protected $foo;

    #[ORM\OneToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Bar')]
    #[ORM\JoinColumn(name: 'bar_id', referencedColumnName: 'bar_id')]
    protected $bar;

    #[ORM\ManyToOne(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id')]
    protected $parent;

    #[ORM\OneToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass', mappedBy: 'parent')]
    protected $children;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    protected $decimalField;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    protected ?string $decimalFieldWithHint;

    #[ORM\Column(type: 'datetime')]
    protected $dateField;

    #[ORM\Column(type: 'boolean')]
    protected $booleanField;

    #[ORM\Column(type: 'text')]
    protected $textField;

    #[ORM\Column(type: 'simple_array')]
    protected $simpleArrayField;

    #[ORM\Column(type: 'json')]
    protected $jsonField;

    #[ORM\Column(type: 'guid')]
    protected $guidField;

    #[ORM\Column(type: 'my_custom_type')]
    protected $customField;

    public function getMyFoo(): ?MyFoo
    {
        return $this->foo;
    }

    /*
     * Getters / Setters (auto-generated)
     */
}
