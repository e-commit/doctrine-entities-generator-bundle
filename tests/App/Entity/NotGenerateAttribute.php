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
use Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\IgnoreGenerateEntity;

#[ORM\Entity]
#[ORM\Table(name: 'not_generate_attribute')]
#[IgnoreGenerateEntity]
class NotGenerateAttribute
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'author_id')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $name = null;

    /*
     * Getters / Setters (auto-generated)
     */
}
