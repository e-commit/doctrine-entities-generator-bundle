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
 *
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class GenerateEntityTemplate
{
    /**
     * @var string
     */
    public $template;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $template = null;
        if (\is_string($data)) {
            $template = $data;
        } elseif (\is_array($data) && isset($data['value'])) {
            $template = $data['value'];
        } elseif (\is_array($data)) {
            $template = $data['template'] ?? null;
        }

        if (null === $template || '' === $template || !\is_string($template)) {
            throw new \BadMethodCallException(sprintf('Missing property "template" on annotation "%s".', static::class));
        }
        $this->template = $template;
    }
}
