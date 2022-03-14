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

use Doctrine\Persistence\ManagerRegistry;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\EntityGenerator;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher\EntitySearcher;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\ClassNotManagedException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\EntityInitializerInterfaceNotUsedException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\TagNotFoundException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Model\GenerateEntityRequest;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\AbstractTest;
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
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\NotEntity;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\NotGenerate;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\NotGeneratePhp8;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\OverrideTemplatePhp8;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\PriceTrait;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Sale;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Twig\Environment;

class EntityGeneratorTest extends AbstractTest
{
    public function testServiceClass(): void
    {
        $service = self::$kernel->getContainer()->get('ecommit_doctrine_entities_generator.entity_generator');
        $this->assertInstanceOf(EntityGenerator::class, $service);
    }

    public function testAliasServiceClass(): void
    {
        $service = self::$kernel->getContainer()->get(EntityGenerator::class);
        $this->assertInstanceOf(EntityGenerator::class, $service);
    }

    /**
     * @dataProvider getTestGetFilePartsValidProvider
     */
    public function testGetFilePartsValid($class, $fixturesDir): void
    {
        $entityGenerator = self::$kernel->getContainer()->get(EntityGenerator::class);
        $reflectionClass = new \ReflectionClass(EntityGenerator::class);
        $method = $reflectionClass->getMethod('getFileParts');
        $method->setAccessible(true);
        $result = $method->invokeArgs($entityGenerator, [new \ReflectionClass($class)]);

        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/beforeBlock.txt'), $result['beforeBlock']);
        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/startTag.txt'), $result['startTag']);
        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/block.txt'), $result['block']);
        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/endTag.txt'), $result['endTag']);
        $this->assertSame(file_get_contents(__DIR__.'/../fixtures/getFileParts/'.$fixturesDir.'/afterBlock.txt'), $result['afterBlock']);
    }

    public function getTestGetFilePartsValidProvider(): array
    {
        return [
            [Author::class, 'author'],
            [Category::class, 'category'],
        ];
    }

    /**
     * @dataProvider getTestGetFilePartsTagNotFoundProvider
     */
    public function testGetFilePartsTagNotFound($template): void
    {
        $entityGenerator = new EntityGenerator(
            $this->getContainer()->get(EntitySearcher::class),
            $this->getContainer()->get(ManagerRegistry::class),
            $this->getContainer()->get(Environment::class),
            $template
        );
        $reflectionClass = new \ReflectionClass(EntityGenerator::class);
        $method = $reflectionClass->getMethod('getFileParts');
        $method->setAccessible(true);

        $this->expectException(TagNotFoundException::class);
        $this->expectExceptionMessage('Class "Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Author": Start tag or end tag is not found');
        $method->invokeArgs($entityGenerator, [new \ReflectionClass(Author::class)]);
    }

    public function getTestGetFilePartsTagNotFoundProvider(): array
    {
        return [
            ['bad_start_tag.php.twig'],
            ['bad_end_tag.php.twig'],
        ];
    }

    public function testRenderBlock(): void
    {
        $entityGenerator = self::$kernel->getContainer()->get(EntityGenerator::class);
        $reflectionClass = new \ReflectionClass(EntityGenerator::class);
        $method = $reflectionClass->getMethod('renderBlock');
        $method->setAccessible(true);
        $result = $method->invokeArgs($entityGenerator, [new \ReflectionClass(Author::class), 'end_tag']);

        $this->assertSame('}', $result);
    }

    /**
     * @dataProvider getTestGenerateClassNotManagedProvider
     */
    public function testGenerateClassNotManaged($class): void
    {
        $this->expectException(ClassNotManagedException::class);
        $this->expectExceptionMessage('Class "'.$class.'" not managed');

        $entityManager = $this->getEntityGeneratorMock();
        $entityManager->generate($class);
    }

    public function getTestGenerateClassNotManagedProvider()
    {
        return [
            [NotEntity::class],
            [PriceTrait::class],
            [\stdClass::class],
        ];
    }

    /**
     * @dataProvider getTestGenerateClassIgnoreProvider
     */
    public function testGenerateClassIgnore($class): void
    {
        $this->expectException(ClassNotManagedException::class);
        $this->expectExceptionMessage('Class "'.$class.'" cannot be generated (Is IgnoreGenerateEntity annotation used ?)');

        $entityManager = $this->getEntityGeneratorMock();
        $entityManager->generate($class);
    }

    public function getTestGenerateClassIgnoreProvider()
    {
        return [
            [NotGenerate::class],
        ];
    }

    /**
     * @dataProvider getTestPropertyIsDefinedInClassFileProvider
     */
    public function testPropertyIsDefinedInClassFile($class, $property, $expectedResult): void
    {
        $entityGenerator = self::$kernel->getContainer()->get(EntityGenerator::class);
        $entityGeneratorReflection = new \ReflectionClass(EntityGenerator::class);
        $getFilePartsReflection = $entityGeneratorReflection->getMethod('getFileParts');
        $getFilePartsReflection->setAccessible(true);
        $propertyIsDefinedInClassFileReflection = $entityGeneratorReflection->getMethod('propertyIsDefinedInClassFile');
        $propertyIsDefinedInClassFileReflection->setAccessible(true);

        $reflectionClass = new \ReflectionClass($class);
        $request = new GenerateEntityRequest(
            $reflectionClass,
            $getFilePartsReflection->invokeArgs($entityGenerator, [$reflectionClass]),
            $this->getContainer()->get(ManagerRegistry::class)->getManagerForClass($class)->getClassMetadata($class),
            new DoctrineExtractor($this->getContainer()->get(ManagerRegistry::class)->getManagerForClass($class))
        );

        $result = $propertyIsDefinedInClassFileReflection->invokeArgs($entityGenerator, [
            $request,
            $property,
        ]);

        $this->assertSame($expectedResult, $result);
    }

    public function getTestPropertyIsDefinedInClassFileProvider(): array
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
     * @dataProvider getTestMethodIsDefinedOutsideBlockProvider
     */
    public function testMethodIsDefinedOutsideBlock($class, $method, $expectedResult): void
    {
        $entityGenerator = self::$kernel->getContainer()->get(EntityGenerator::class);
        $entityGeneratorReflection = new \ReflectionClass(EntityGenerator::class);
        $getFilePartsReflection = $entityGeneratorReflection->getMethod('getFileParts');
        $getFilePartsReflection->setAccessible(true);
        $propertyIsDefinedInClassFileReflection = $entityGeneratorReflection->getMethod('methodIsDefinedOutsideBlock');
        $propertyIsDefinedInClassFileReflection->setAccessible(true);

        $reflectionClass = new \ReflectionClass($class);
        $request = new GenerateEntityRequest(
            $reflectionClass,
            $getFilePartsReflection->invokeArgs($entityGenerator, [$reflectionClass]),
            $this->getContainer()->get(ManagerRegistry::class)->getManagerForClass($class)->getClassMetadata($class),
            new DoctrineExtractor($this->getContainer()->get(ManagerRegistry::class)->getManagerForClass($class))
        );

        $result = $propertyIsDefinedInClassFileReflection->invokeArgs($entityGenerator, [
            $request,
            $method,
        ]);

        $this->assertSame($expectedResult, $result);
    }

    public function getTestMethodIsDefinedOutsideBlockProvider(): array
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
    public function testBuildMethodName($type, $fieldName, $expectedResult): void
    {
        $entityGenerator = self::$kernel->getContainer()->get(EntityGenerator::class);
        $entityGeneratorReflection = new \ReflectionClass(EntityGenerator::class);
        $method = $entityGeneratorReflection->getMethod('buildMethodName');
        $method->setAccessible(true);
        $result = $method->invokeArgs($entityGenerator, [$type, $fieldName]);

        $this->assertSame($expectedResult, $result);
    }

    public function getTestBuildMethodNameProdiver(): array
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
    public function testBuildVariableName($type, $variableName, $expectedResult): void
    {
        $entityGenerator = self::$kernel->getContainer()->get(EntityGenerator::class);
        $entityGeneratorReflection = new \ReflectionClass(EntityGenerator::class);
        $method = $entityGeneratorReflection->getMethod('buildVariableName');
        $method->setAccessible(true);
        $result = $method->invokeArgs($entityGenerator, [$type, $variableName]);

        $this->assertSame($expectedResult, $result);
    }

    public function getTestBuildVariableNameProvider(): array
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
     * @dataProvider getTestGenerateProvider
     */
    public function testGenerate($class): void
    {
        $entityManager = $this->getEntityGeneratorMock();
        $entityManager->generate($class);

        $this->checkGeneratedClass($class);
    }

    public function getTestGenerateProvider(): array
    {
        $data = [
            [Author::class],
            [Book::class],
            [Category::class],
            [Initializer1::class],
            [Initializer2::class],
            [Initializer3::class],
            [MainClass::class],
            [OverrideTemplatePhp8::class],
            [Sale::class],
            [SubClass::class],
            [Foo::class],
            [Bar::class],
        ];

        if (\PHP_VERSION_ID < 80000) {
            $data[] = [NotGeneratePhp8::class];
        }

        return $data;
    }

    /**
     * @dataProvider getTestGenerateEntityInitializerInterfaceNotUsedExceptionProdiver
     */
    public function testGenerateEntityInitializerInterfaceNotUsedException($class): void
    {
        $this->expectException(EntityInitializerInterfaceNotUsedException::class);
        $this->expectExceptionMessage('Class "'.$class.'": __construct method is used. Remove it and implement "Ecommit\DoctrineEntitiesGeneratorBundle\Entity\EntityInitializerInterface" interface');

        $entityManager = $this->getEntityGeneratorMock();
        $entityManager->generate($class);
    }

    public function getTestGenerateEntityInitializerInterfaceNotUsedExceptionProdiver()
    {
        return [
            [Initializer4::class],
            [Initializer5::class],
        ];
    }
}
