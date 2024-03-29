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
use Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\GenerateEntityTemplate;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[GenerateEntityTemplate('custom_end_tag.php.twig')]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'category_id')]
    protected $categoryId;

    #[ORM\Column(type: 'string', length: 255)]
    protected $name;

    #[ORM\Column(type: 'my_custom_type')]
    protected $customField;

    #[ORM\OneToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Book', mappedBy: 'category')]
    protected $books;

    public function methodBeforeBlock(): string
    {
        return 'OK';
    }

    /*
     * Getters / Setters (auto-generated)
     */

    public function getName(): string
    {
        return 'OK';
    }

    /*
     * End Getters / Setters (auto-generated)
     */

    public function methodAfterBlock(): string
    {
        return 'OK';
    }
}
