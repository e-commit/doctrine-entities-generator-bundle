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

trait PriceTrait
{
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    protected ?string $price = null;

    public function renderPrice(): string
    {
        return (string) $this->price;
    }
}
