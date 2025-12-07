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

use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\Util\TwigHelper;
use PHPUnit\Framework\TestCase;

class TwigHelperTest extends TestCase
{
    /**
     * @dataProvider getTestIsProvider
     *
     * @param class-string $class
     */
    public function testIs(?object $object, string $class, bool $expected): void
    {
        $this->assertSame($expected, (new TwigHelper())->is($object, $class));
    }

    public static function getTestIsProvider(): array
    {
        return [
            [null, \DateTime::class, false],
            [new \stdClass(), \DateTime::class, false],
            [new \DateTime(), \DateTime::class, true],
            [new \DateTime(), \DateTimeInterface::class, true],
        ];
    }
}
