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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\EntityGenerator\Util;

use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\Util\SourcePropertyTypeResolver;
use PHPUnit\Framework\TestCase;

class SourcePropertyTypeResolverTest extends TestCase
{
    public function testGetTypeNotNullableType(): void
    {
        $sourceCode = <<<'CODE'
            <?php
            namespace Foo\Bar;

            class MyClass
            {
                protected string $field;
            }
            CODE;
        $resolver = new SourcePropertyTypeResolver($sourceCode);
        $this->assertSame('string', $resolver->getType('field'));
    }

    public function testGetTypeNullableType(): void
    {
        $sourceCode = <<<'CODE'
            <?php
            namespace Foo\Bar;

            class MyClass
            {
                protected ?string $field = null;
            }
            CODE;
        $resolver = new SourcePropertyTypeResolver($sourceCode);
        $this->assertSame('?string', $resolver->getType('field'));
    }

    public function testGetTypeUnionType(): void
    {
        $sourceCode = <<<'CODE'
            <?php
            namespace Foo\Bar;

            class MyClass
            {
                protected string|int $field;
            }
            CODE;
        $resolver = new SourcePropertyTypeResolver($sourceCode);
        $this->assertSame('string|int', $resolver->getType('field'));
    }

    public function testGetTypeBytes(): void
    {
        $sourceCode = <<<'CODE'
            <?php
            namespace Foo\Bar;

            class MyClass
            {
                // Comment & é  (bytes != chars)
                protected string $field;
            }
            CODE;
        $resolver = new SourcePropertyTypeResolver($sourceCode);
        $this->assertSame('string', $resolver->getType('field'));
    }

    public function testGetTypePropertyNotFound(): void
    {
        $sourceCode = <<<'CODE'
            <?php
            namespace Foo\Bar;

            class MyClass
            {
                // Comment & é  (bytes != chars)
                protected string $field;
            }
            CODE;
        $resolver = new SourcePropertyTypeResolver($sourceCode);
        $this->assertNull($resolver->getType('bad'));
    }
}
