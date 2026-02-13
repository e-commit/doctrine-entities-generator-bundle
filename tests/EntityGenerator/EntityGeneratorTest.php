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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\EntityGenerator;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\EntityGenerator;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher\EntitySearcher;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\ClassNotManagedException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\EntityInitializerInterfaceNotUsedException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\TagNotFoundException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Model\GenerateEntityRequest;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\AbstractTestCase;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Address;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Author;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Bigint;
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
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\NotEntity;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\NotGenerateAttribute;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutType;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OtherNamespace\WithoutTypeRelation;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OverrideTemplate;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\PriceTrait;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\ReadOnlyField;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Sale;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\WithEnum;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\WithNotNull;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Twig\Environment;

/**
 * @phpstan-import-type FileParts from GenerateEntityRequest
 */
class EntityGeneratorTest extends AbstractTestCase
{
    public function testServiceIsPrivate(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        self::$kernel?->getContainer()->get('ecommit_doctrine_entities_generator.entity_generator');
    }

    public function testAliasServiceIsPrivate(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        self::$kernel?->getContainer()->get(EntityGenerator::class);
    }

    public function testServiceClass(): void
    {
        $service = self::getContainer()->get('ecommit_doctrine_entities_generator.entity_generator');
        $this->assertInstanceOf(EntityGenerator::class, $service);
    }

    public function testAliasServiceClass(): void
    {
        $service = self::getContainer()->get(EntityGenerator::class);
        $this->assertInstanceOf(EntityGenerator::class, $service);
    }

    /**
     * @param class-string $class
     *
     * @dataProvider getTestGetFilePartsValidProvider
     */
    public function testGetFilePartsValid(string $class, string $fixturesDir): void
    {
        $entityGenerator = self::getContainer()->get(EntityGenerator::class);
        $reflectionClass = new \ReflectionClass(EntityGenerator::class);
        $method = $reflectionClass->getMethod('getFileParts');
        $method->setAccessible(true);
        /** @var FileParts $result */
        $result = $method->invokeArgs($entityGenerator, [new \ReflectionClass($class)]);

        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/beforeBlock.txt'), $result['beforeBlock']);
        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/startTag.txt'), $result['startTag']);
        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/block.txt'), $result['block']);
        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/endTag.txt'), $result['endTag']);
        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/afterBlock.txt'), $result['afterBlock']);
    }

    public static function getTestGetFilePartsValidProvider(): array
    {
        return [
            [Author::class, 'author'],
            [Category::class, 'category'],
        ];
    }

    /**
     * @dataProvider getTestGetFilePartsTagNotFoundProvider
     */
    public function testGetFilePartsTagNotFound(string $template): void
    {
        $entityGenerator = new EntityGenerator(
            self::getContainer()->get(EntitySearcher::class),
            self::getContainer()->get(ManagerRegistry::class),
            self::getContainer()->get(Environment::class),
            $template
        );
        $reflectionClass = new \ReflectionClass(EntityGenerator::class);
        $method = $reflectionClass->getMethod('getFileParts');
        $method->setAccessible(true);

        $this->expectException(TagNotFoundException::class);
        $this->expectExceptionMessage('Class "Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Author": Start tag or end tag is not found');
        $method->invokeArgs($entityGenerator, [new \ReflectionClass(Author::class)]);
    }

    public static function getTestGetFilePartsTagNotFoundProvider(): array
    {
        return [
            ['bad_start_tag.php.twig'],
            ['bad_end_tag.php.twig'],
        ];
    }

    public function testRenderBlock(): void
    {
        $entityGenerator = self::getContainer()->get(EntityGenerator::class);
        $reflectionClass = new \ReflectionClass(EntityGenerator::class);
        $method = $reflectionClass->getMethod('renderBlock');
        $method->setAccessible(true);
        $result = $method->invokeArgs($entityGenerator, [new \ReflectionClass(Author::class), 'end_tag']);

        $this->assertSame('}', $result);
    }

    /**
     * @param class-string $class
     *
     * @dataProvider getTestGenerateClassNotManagedProvider
     */
    public function testGenerateClassNotManaged(string $class): void
    {
        $this->expectException(ClassNotManagedException::class);
        $this->expectExceptionMessage('Class "'.$class.'" not managed');

        $entityManager = $this->getEntityGeneratorMock();
        $entityManager->generate($class);
    }

    public static function getTestGenerateClassNotManagedProvider(): array
    {
        return [
            [NotEntity::class],
            [PriceTrait::class],
            [\stdClass::class],
        ];
    }

    /**
     * @param class-string $class
     *
     * @dataProvider getTestGenerateClassIgnoreProvider
     */
    public function testGenerateClassIgnore(string $class): void
    {
        $this->expectException(ClassNotManagedException::class);
        $this->expectExceptionMessage('Class "'.$class.'" cannot be generated (Is IgnoreGenerateEntity attribute used ?)');

        $entityManager = $this->getEntityGeneratorMock();
        $entityManager->generate($class);
    }

    public static function getTestGenerateClassIgnoreProvider(): array
    {
        return [
            [
                NotGenerateAttribute::class,
            ],
        ];
    }

    /**
     * @param class-string $class
     *
     * @dataProvider getTestPropertyIsDefinedInClassFileProvider
     */
    public function testPropertyIsDefinedInClassFile(string $class, string $property, bool $expectedResult): void
    {
        $entityGenerator = self::getContainer()->get(EntityGenerator::class);
        $entityGeneratorReflection = new \ReflectionClass(EntityGenerator::class);
        $getFilePartsReflection = $entityGeneratorReflection->getMethod('getFileParts');
        $getFilePartsReflection->setAccessible(true);
        $propertyIsDefinedInClassFileReflection = $entityGeneratorReflection->getMethod('propertyIsDefinedInClassFile');
        $propertyIsDefinedInClassFileReflection->setAccessible(true);

        $reflectionClass = new \ReflectionClass($class);
        /** @var FileParts $fileParts */
        $fileParts = $getFilePartsReflection->invokeArgs($entityGenerator, [$reflectionClass]);
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get(ManagerRegistry::class)->getManagerForClass($class);
        $request = new GenerateEntityRequest(
            $reflectionClass,
            $fileParts,
            $em->getClassMetadata($class),
            new DoctrineExtractor($em)
        );

        $result = $propertyIsDefinedInClassFileReflection->invokeArgs($entityGenerator, [
            $request,
            $property,
        ]);

        $this->assertSame($expectedResult, $result);
    }

    public static function getTestPropertyIsDefinedInClassFileProvider(): array
    {
        return [
            [Author::class, 'authorId', true],
            [Author::class, 'badProperty', false],

            // Extends
            [MainClass::class, 'id', true],
            [MainClass::class, 'toto', true],
            [MainClass::class, 'tutu', true],
            [SubClass::class, 'id', false],

            // Public
            [Author::class, 'phoneNumber', false],

            // Defined in trait
            [Book::class, 'price', false],
        ];
    }

    /**
     * @param class-string $class
     *
     * @dataProvider getTestMethodIsDefinedOutsideBlockProvider
     */
    public function testMethodIsDefinedOutsideBlock(string $class, string $method, bool $expectedResult): void
    {
        $entityGenerator = self::getContainer()->get(EntityGenerator::class);
        $entityGeneratorReflection = new \ReflectionClass(EntityGenerator::class);
        $getFilePartsReflection = $entityGeneratorReflection->getMethod('getFileParts');
        $getFilePartsReflection->setAccessible(true);
        $propertyIsDefinedInClassFileReflection = $entityGeneratorReflection->getMethod('methodIsDefinedOutsideBlock');
        $propertyIsDefinedInClassFileReflection->setAccessible(true);

        $reflectionClass = new \ReflectionClass($class);
        /** @var FileParts $fileParts */
        $fileParts = $getFilePartsReflection->invokeArgs($entityGenerator, [$reflectionClass]);
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get(ManagerRegistry::class)->getManagerForClass($class);
        $request = new GenerateEntityRequest(
            $reflectionClass,
            $fileParts,
            $em->getClassMetadata($class),
            new DoctrineExtractor($em)
        );

        $result = $propertyIsDefinedInClassFileReflection->invokeArgs($entityGenerator, [
            $request,
            $method,
        ]);

        $this->assertSame($expectedResult, $result);
    }

    public static function getTestMethodIsDefinedOutsideBlockProvider(): array
    {
        return [
            [Author::class, 'badMethod', false],

            // Extends
            [SubClass::class, 'getMainMethod', true],

            // Defined in trait
            [Book::class, 'renderPrice', true],

            // Before block
            [Author::class, 'getFullName', true],
            [Category::class, 'methodBeforeBlock', true],

            // After block
            [Category::class, 'methodAfterBlock', true],

            // Inside block
            [Category::class, 'getName', false],
        ];
    }

    /**
     * @dataProvider getTestBuildMethodNameProdiver
     */
    public function testBuildMethodName(string $type, string $fieldName, string $expectedResult): void
    {
        $entityGenerator = self::getContainer()->get(EntityGenerator::class);
        $entityGeneratorReflection = new \ReflectionClass(EntityGenerator::class);
        $method = $entityGeneratorReflection->getMethod('buildMethodName');
        $method->setAccessible(true);
        $result = $method->invokeArgs($entityGenerator, [$type, $fieldName]);

        $this->assertSame($expectedResult, $result);
    }

    public static function getTestBuildMethodNameProdiver(): array
    {
        return [
            [EntityGenerator::TYPE_GET, 'company', 'getCompany'],
            [EntityGenerator::TYPE_SET, 'company', 'setCompany'],
            [EntityGenerator::TYPE_ADD, 'company', 'addCompany'],
            [EntityGenerator::TYPE_REMOVE, 'company', 'removeCompany'],

            [EntityGenerator::TYPE_GET, 'companies', 'getCompanies'],
            [EntityGenerator::TYPE_SET, 'companies', 'setCompanies'],
            [EntityGenerator::TYPE_ADD, 'companies', 'addCompany'],
            [EntityGenerator::TYPE_REMOVE, 'companies', 'removeCompany'],

            [EntityGenerator::TYPE_GET, 'my_company', 'getMyCompany'],
            [EntityGenerator::TYPE_SET, 'my_company', 'setMyCompany'],
            [EntityGenerator::TYPE_ADD, 'my_company', 'addMyCompany'],
            [EntityGenerator::TYPE_REMOVE, 'my_company', 'removeMyCompany'],

            [EntityGenerator::TYPE_GET, 'my_companies', 'getMyCompanies'],
            [EntityGenerator::TYPE_SET, 'my_companies', 'setMyCompanies'],
            [EntityGenerator::TYPE_ADD, 'my_companies', 'addMyCompany'],
            [EntityGenerator::TYPE_REMOVE, 'my_companies', 'removeMyCompany'],
        ];
    }

    /**
     * @dataProvider getTestBuildVariableNameProvider
     */
    public function testBuildVariableName(string $type, string $variableName, string $expectedResult): void
    {
        $entityGenerator = self::getContainer()->get(EntityGenerator::class);
        $entityGeneratorReflection = new \ReflectionClass(EntityGenerator::class);
        $method = $entityGeneratorReflection->getMethod('buildVariableName');
        $method->setAccessible(true);
        $result = $method->invokeArgs($entityGenerator, [$type, $variableName]);

        $this->assertSame($expectedResult, $result);
    }

    public static function getTestBuildVariableNameProvider(): array
    {
        return [
            [EntityGenerator::TYPE_GET, 'company', 'company'],
            [EntityGenerator::TYPE_SET, 'company', 'company'],
            [EntityGenerator::TYPE_ADD, 'company', 'company'],
            [EntityGenerator::TYPE_REMOVE, 'company', 'company'],

            [EntityGenerator::TYPE_GET, 'companies', 'companies'],
            [EntityGenerator::TYPE_SET, 'companies', 'companies'],
            [EntityGenerator::TYPE_ADD, 'companies', 'company'],
            [EntityGenerator::TYPE_REMOVE, 'companies', 'company'],

            [EntityGenerator::TYPE_GET, 'my_company', 'myCompany'],
            [EntityGenerator::TYPE_SET, 'my_company', 'myCompany'],
            [EntityGenerator::TYPE_ADD, 'my_company', 'myCompany'],
            [EntityGenerator::TYPE_REMOVE, 'my_company', 'myCompany'],

            [EntityGenerator::TYPE_GET, 'my_companies', 'myCompanies'],
            [EntityGenerator::TYPE_SET, 'my_companies', 'myCompanies'],
            [EntityGenerator::TYPE_ADD, 'my_companies', 'myCompany'],
            [EntityGenerator::TYPE_REMOVE, 'my_companies', 'myCompany'],
        ];
    }

    /**
     * @param class-string $class
     *
     * @dataProvider getTestGenerateProvider
     */
    public function testGenerate(string $class, ?string $folder = null): void
    {
        $entityManager = $this->getEntityGeneratorMock();
        $entityManager->generate($class);

        $this->checkGeneratedClass($class, $folder);
    }

    public static function getTestGenerateProvider(): array
    {
        /**
         * @legacy Support for doctrine/dbal v3
         */
        $isDbal3 = method_exists(AbstractMySQLPlatform::class, 'getColumnTypeSQLSnippets');

        $data = [
            [Address::class],
            [Author::class],
            [Bigint::class, $isDbal3 ? 'GeneratedEntityDbal3' : null],
            [Book::class],
            [Category::class],
            [Initializer1::class],
            [Initializer2::class],
            [Initializer3::class],
            [MainClass::class],
            [OverrideTemplate::class],
            [ReadOnlyField::class],
            [Sale::class],
            [SubClass::class],
            [Foo::class],
            [Bar::class],
            [WithEnum::class],
            [WithNotNull::class],
            [WithoutType::class],
            [WithoutTypeRelation::class],
        ];

        return $data;
    }

    /**
     * @param class-string $class
     *
     * @dataProvider getTestGenerateEntityInitializerInterfaceNotUsedExceptionProdiver
     */
    public function testGenerateEntityInitializerInterfaceNotUsedException(string $class): void
    {
        $this->expectException(EntityInitializerInterfaceNotUsedException::class);
        $this->expectExceptionMessage('Class "'.$class.'": __construct method is used. Remove it and implement "Ecommit\DoctrineEntitiesGeneratorBundle\Entity\EntityInitializerInterface" interface');

        $entityManager = $this->getEntityGeneratorMock();
        $entityManager->generate($class);
    }

    public static function getTestGenerateEntityInitializerInterfaceNotUsedExceptionProdiver(): array
    {
        return [
            [Initializer4::class],
            [Initializer5::class],
        ];
    }
}
