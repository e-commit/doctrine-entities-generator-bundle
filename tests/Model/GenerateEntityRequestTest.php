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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\Model;

use Ecommit\DoctrineEntitiesGeneratorBundle\Model\GenerateEntityRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GenerateEntityRequestTest extends TestCase
{
    /**
     * @param class-string $class
     */
    #[DataProvider('getTestIsProvider')]
    public function testIs(?object $object, string $class, bool $expected): void
    {
        $request = $this->getMockBuilder(GenerateEntityRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->assertSame($expected, $request->is($object, $class));
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
