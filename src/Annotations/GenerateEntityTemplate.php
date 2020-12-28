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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Annotations;

use Attribute;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class GenerateEntityTemplate
{
    public $template;

    public function __construct($data)
    {
        if (\is_string($data)) {
            $this->template = $data;
        } elseif (isset($data['value'])) {
            $this->template = $data['value'];
        } else {
            $this->template = $data['template'] ?? null;
        }

        if (null === $this->template || '' === $this->template || !\is_string($this->template)) {
            throw new \BadMethodCallException(sprintf('Missing property "template" on annotation "%s".', static::class));
        }
    }
}
