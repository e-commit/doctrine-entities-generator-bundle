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
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer1;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer2;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer3;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer4;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Initializer5;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\MainClass;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\NotGenerate;
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
        self::$kernel->getContainer()->get('ecommit_doctrine_entities_generator.entity_searcher');
    }

    public function testAliasServiceIsPrivate(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        self::$kernel->getContainer()->get(EntitySearcher::class);
    }

    public function testServiceClass(): void
    {
        $service = self::$container->get('ecommit_doctrine_entities_generator.entity_searcher');
        $this->assertInstanceOf(EntitySearcher::class, $service);
    }

    public function testAliasServiceClass(): void
    {
        $service = self::$container->get(EntitySearcher::class);
        $this->assertInstanceOf(EntitySearcher::class, $service);
    }

    /**
     * @dataProvider getTestInputMatchesClassProvider
     */
    public function testInputMatchesClass($input, $expectedResult): void
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

    public function getTestInputMatchesClassProvider(): array
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
     * @dataProvider getTestClassCanBeGeneratedProvider
     */
    public function testClassCanBeGenerated($class, $expectedResult): void
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::$kernel->getContainer()->get('doctrine');
        $metadata = $managerRegistry->getManager()->getClassMetadata($class);

        $entitySearcher = new EntitySearcher($managerRegistry);
        $result = $entitySearcher->classCanBeGenerated($metadata);

        $this->assertSame($expectedResult, $result);
    }

    public function getTestClassCanBeGeneratedProvider()
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
            [NotGenerate::class, false],
            [Sale::class, true],
            [SubClass::class, true],
        ];
    }

    /**
     * @dataProvider getTestProvider
     */
    public function testSearchInManager($input, $expectedResult): void
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::$kernel->getContainer()->get('doctrine');
        $manager = $managerRegistry->getManager();

        $entitySearcher = new EntitySearcher($managerRegistry);
        $reflectionClass = new \ReflectionClass(EntitySearcher::class);
        $method = $reflectionClass->getMethod('searchInManager');
        $method->setAccessible(true);
        $result = $method->invokeArgs($entitySearcher, [
            $manager,
            $input,
        ]);

        sort($result);

        $this->assertSame($expectedResult, $result);
    }

    public function getTestProvider()
    {
        $data = [];

        $data[] = ['bad', []];

        $data[] = ['*', [
            Author::class,
            Book::class,
            Category::class,
            Initializer1::class,
            Initializer2::class,
            Initializer3::class,
            Initializer4::class,
            Initializer5::class,
            MainClass::class,
            Sale::class,
            SubClass::class,
        ]];

        $data[] = ['Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity', []];

        $data[] = ['Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\*', [
            Author::class,
            Book::class,
            Category::class,
            Initializer1::class,
            Initializer2::class,
            Initializer3::class,
            Initializer4::class,
            Initializer5::class,
            MainClass::class,
            Sale::class,
            SubClass::class,
        ]];

        $data[] = ['Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\S*', [
            Sale::class,
            SubClass::class,
        ]];

        $data[] = ['Ecommit/DoctrineEntitiesGeneratorBundle/Tests/App/Entity', []];

        $data[] = ['Ecommit/DoctrineEntitiesGeneratorBundle/Tests/App/Entity/*', [
            Author::class,
            Book::class,
            Category::class,
            Initializer1::class,
            Initializer2::class,
            Initializer3::class,
            Initializer4::class,
            Initializer5::class,
            MainClass::class,
            Sale::class,
            SubClass::class,
        ]];

        $data[] = ['Ecommit/DoctrineEntitiesGeneratorBundle/Tests/App/Entity/S*', [
            Sale::class,
            SubClass::class,
        ]];

        return $data;
    }

    /**
     * @dataProvider getTestProvider
     */
    public function testSearch($input, $expectedResult): void
    {
        $result = self::$container->get(EntitySearcher::class)->search($input);

        sort($result);

        $this->assertSame($expectedResult, $result);
    }
}
