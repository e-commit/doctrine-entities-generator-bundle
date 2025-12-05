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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\EntitySearcher;

use Doctrine\Persistence\ManagerRegistry;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher\EntitySearcher;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Author;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Book;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Category;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Bar;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Foo\Foo;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer1;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer2;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer3;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer4;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer5;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\MainClass;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\NotGenerateAttribute;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OverrideTemplate;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Sale;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class EntitySearcherTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testServiceIsPrivate(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        self::$kernel->getContainer()->get('ecommit_doctrine_entities_generator.entity_searcher'); // @phpstan-ignore-line
    }

    public function testAliasServiceIsPrivate(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        self::$kernel->getContainer()->get(EntitySearcher::class); // @phpstan-ignore-line
    }

    public function testServiceClass(): void
    {
        $service = self::getContainer()->get('ecommit_doctrine_entities_generator.entity_searcher');
        $this->assertInstanceOf(EntitySearcher::class, $service);
    }

    public function testAliasServiceClass(): void
    {
        $service = self::getContainer()->get(EntitySearcher::class);
        $this->assertInstanceOf(EntitySearcher::class, $service);
    }

    /**
     * @dataProvider getTestInputMatchesClassProvider
     */
    public function testInputMatchesClass(string $input, bool $expectedResult): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $entitySearcher = new EntitySearcher($managerRegistry);
        $reflectionClass = new \ReflectionClass(EntitySearcher::class);
        $method = $reflectionClass->getMethod('inputMatchesClass');
        $method->setAccessible(true);
        $result = $method->invokeArgs($entitySearcher, [
            'Foo\Bar\Class',
            $input,
        ]);

        $this->assertSame($expectedResult, $result);
    }

    public static function getTestInputMatchesClassProvider(): array
    {
        return [
            ['Foo\Bar\Class', true],
            ['Foo\Bar', false],
            ['Bar\Class', false],
            ['Bar', false],
            ['*Foo\Bar\Class', false],
            ['Foo\Bar\Class*', false],
            ['*Foo\Bar\Class*', false],
            ['*Foo\Bar', false],
            ['Foo\Bar*', true],
            ['*Foo\Bar*', false],
            ['*Bar\Class', true],
            ['Bar\Class*', false],
            ['*Bar\Class*', false],
            ['*Bar', false],
            ['Bar*', false],
            ['*Bar*', true],

            ['Foo/Bar/Class', true],
            ['Foo/Bar', false],
            ['Bar/Class', false],
            ['Bar', false],
            ['*Foo/Bar/Class', false],
            ['Foo/Bar/Class*', false],
            ['*Foo/Bar/Class*', false],
            ['*Foo/Bar', false],
            ['Foo/Bar*', true],
            ['*Foo/Bar*', false],
            ['*Bar/Class', true],
            ['Bar/Class*', false],
            ['*Bar/Class*', false],
            ['*Bar', false],
            ['Bar*', false],
            ['*Bar*', true],
        ];
    }

    /**
     * @param class-string<object> $class
     *
     * @dataProvider getTestClassCanBeGeneratedProvider
     */
    public function testClassCanBeGenerated(string $class, bool $expectedResult): void
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::$kernel->getContainer()->get('doctrine');
        $metadata = $managerRegistry->getManager()->getClassMetadata($class);

        $entitySearcher = new EntitySearcher($managerRegistry);
        $result = $entitySearcher->classCanBeGenerated($metadata);

        $this->assertSame($expectedResult, $result);
    }

    public static function getTestClassCanBeGeneratedProvider(): array
    {
        return [
            [Author::class, true],
            [Book::class, true],
            [Category::class, true],
            [Initializer1::class, true],
            [Initializer2::class, true],
            [Initializer3::class, true],
            [Initializer4::class, true],
            [Initializer5::class, true],
            [MainClass::class, true],
            [NotGenerateAttribute::class, false],
            [Sale::class, true],
            [SubClass::class, true],
        ];
    }

    /**
     * @dataProvider getTestProvider
     */
    public function testSearchInManager(string $input, array $expectedResult): void
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::$kernel->getContainer()->get('doctrine');
        $manager = $managerRegistry->getManager();

        $entitySearcher = new EntitySearcher($managerRegistry);
        $reflectionClass = new \ReflectionClass(EntitySearcher::class);
        $method = $reflectionClass->getMethod('searchInManager');
        $method->setAccessible(true);
        /** @var array $result */
        $result = $method->invokeArgs($entitySearcher, [
            $manager,
            $input,
        ]);

        sort($result);

        $this->assertSame($expectedResult, $result);
    }

    public static function getTestProvider(): array
    {
        $data = [];

        $data[] = ['bad', []];

        $data[] = ['Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity', []];

        $data[] = ['Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\S*', [
            Sale::class,
            SubClass::class,
        ]];

        $data[] = ['Ecommit/DoctrineEntitiesGeneratorBundle/Tests/App/Entity', []];

        $data[] = ['Ecommit/DoctrineEntitiesGeneratorBundle/Tests/App/Entity/S*', [
            Sale::class,
            SubClass::class,
        ]];

        $data[] = ['Ecommit/DoctrineEntitiesGeneratorBundle/Tests/App/Entity/Foo/*', [
            Bar::class,
            Foo::class,
        ]];

        $data[] = ['*', [
            Author::class,
            Book::class,
            Category::class,
            Bar::class,
            Foo::class,
            Initializer1::class,
            Initializer2::class,
            Initializer3::class,
            Initializer4::class,
            Initializer5::class,
            MainClass::class,
            OverrideTemplate::class,
            Sale::class,
            SubClass::class,
        ]];

        $data[] = ['Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\*', [
            Author::class,
            Book::class,
            Category::class,
            Bar::class,
            Foo::class,
            Initializer1::class,
            Initializer2::class,
            Initializer3::class,
            Initializer4::class,
            Initializer5::class,
            MainClass::class,
            OverrideTemplate::class,
            Sale::class,
            SubClass::class,
        ]];

        $data[] = ['Ecommit/DoctrineEntitiesGeneratorBundle/Tests/App/Entity/*', [
            Author::class,
            Book::class,
            Category::class,
            Bar::class,
            Foo::class,
            Initializer1::class,
            Initializer2::class,
            Initializer3::class,
            Initializer4::class,
            Initializer5::class,
            MainClass::class,
            OverrideTemplate::class,
            Sale::class,
            SubClass::class,
        ]];

        return $data;
    }

    /**
     * @dataProvider getTestProvider
     */
    public function testSearch(string $input, array $expectedResult): void
    {
        $result = self::getContainer()->get(EntitySearcher::class)->search($input);

        sort($result);

        $this->assertSame($expectedResult, $result);
    }
}
