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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Author;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Book;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Category;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer1;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\Initializer2;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\MyObject;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity\SubClass;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntityKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeneratedEntityTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    protected $databaseIsInitialized = false;

    protected static function getKernelClass(): string
    {
        return GeneratedEntityKernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::$kernel->getContainer()->get('doctrine')->getManager();

        if (false === $this->databaseIsInitialized) {
            $schemaTool = new SchemaTool($this->em);
            $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
            $schemaTool->updateSchema($metadatas);

            $this->databaseIsInitialized = true;
        }

        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->rollBack();
        $this->em->clear();

        parent::tearDown();
    }

    public function testMapping(): void
    {
        $validator = new SchemaValidator($this->em);
        $this->assertEmpty($validator->validateMapping());
    }

    public function testStringField(): void
    {
        $subClass = $this->createSubClass(2);
        $this->assertSame('Name 2', $subClass->getName());
        $this->assertSame('Text 2', $subClass->getTextField());
        $this->assertSame('GUID 2', $subClass->getGuidField());
        $this->assertSame('Custom 2', $subClass->getCustomField());
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertSame('Name 2', $subClass->getName());
        $this->assertSame('Text 2', $subClass->getTextField());
        $this->assertSame('GUID 2', $subClass->getGuidField());
        $this->assertSame('Custom 2', $subClass->getCustomField());
    }

    public function testIntField(): void
    {
        $subClass = $this->createSubClass(2);
        $this->assertSame(2, $subClass->getId());
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertSame(2, $subClass->getId());
    }

    public function testDecimalField(): void
    {
        $subClass = $this->createSubClass(2);
        $this->assertSame(0.55, $subClass->getDecimalField());
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertSame('0.55', $subClass->getDecimalField());
    }

    public function testDecimalFieldWithHint(): void
    {
        $subClass = $this->createSubClass(2);
        $this->assertSame('0.65', $subClass->getDecimalFieldWithHint());
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertSame('0.65', $subClass->getDecimalFieldWithHint());
    }

    public function testDateField(): void
    {
        $subClass = $this->createSubClass(2);
        $this->assertEquals(new \DateTime('2020-01-01 00:00:00'), $subClass->getDateField());
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertEquals(new \DateTime('2020-01-01 00:00:00'), $subClass->getDateField());
    }

    public function testBooleanField(): void
    {
        $subClass = $this->createSubClass(2);
        $this->assertTrue($subClass->getBooleanField());
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertTrue($subClass->getBooleanField());
    }

    public function testObjectField(): void
    {
        $subClass = $this->createSubClass(2);
        $this->assertInstanceOf(MyObject::class, $subClass->getObjectField());
        $this->assertSame('world 2', $subClass->getObjectField()->hello);
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertInstanceOf(MyObject::class, $subClass->getObjectField());
        $this->assertSame('world 2', $subClass->getObjectField()->hello);
    }

    public function testArrayField(): void
    {
        $subClass = $this->createSubClass(2);
        $this->assertSame(['a' => 2], $subClass->getArrayField());
        $this->assertSame(['b' => 2], $subClass->getSimpleArrayField());
        $this->assertSame(['c' => 2], $subClass->getJsonField());
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertSame(['a' => 2], $subClass->getArrayField());
        $this->assertSame([0 => '2'], $subClass->getSimpleArrayField());
        $this->assertSame(['c' => 2], $subClass->getJsonField());
    }

    /**
     * @dataProvider getTestSetNullFieldProvider
     */
    public function testSetNullField($setter, $getter): void
    {
        $subClass = new SubClass();
        $subClass->$setter(null);
        $this->assertNull($subClass->$getter());
    }

    public function getTestSetNullFieldProvider(): array
    {
        return [
            ['setName', 'getName'],
            ['setDecimalField', 'getDecimalField'],
            ['setDecimalFieldWithHint', 'getDecimalFieldWithHint'],
            ['setDateField', 'getDateField'],
            ['setBooleanField', 'getBooleanField'],
            ['setTextField', 'getTextField'],
            ['setObjectField', 'getObjectField'],
            ['setArrayField', 'getArrayField'],
            ['setSimpleArrayField', 'getSimpleArrayField'],
            ['setJsonField', 'getJsonField'],
            ['setGuidField', 'getGuidField'],
            ['setCustomField', 'getCustomField'],
        ];
    }

    public function testAssociationOneToOneUnidirectional(): void
    {
        $firstInitializer = $this->createInitializer2();
        $subClass = $this->createSubClass(2, null, $firstInitializer);
        $this->assertInstanceOf(Initializer2::class, $subClass->getSecondInitializer());
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertInstanceOf(Initializer2::class, $subClass->getSecondInitializer());
        $this->assertSame(1, $subClass->getSecondInitializer()->getId());

        $subClass->setSecondInitializer(null);
        $this->assertNull($subClass->getSecondInitializer());
        $this->em->flush();
        $this->em->clear();

        $subClass = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertNull($subClass->getSecondInitializer());
    }

    public function testAssociationOneToOneReverse(): void
    {
        $sub = $this->createSubClass(2);
        $firstInitializer = $this->createInitializer1(1, $sub);
        $this->assertInstanceOf(SubClass::class, $firstInitializer->getSub());
        $this->assertSame(2, $firstInitializer->getSub()->getId());
        $this->assertInstanceOf(Initializer1::class, $sub->getFirstInitializer());
        $this->assertSame('Initializer1 name 1', $sub->getFirstInitializer()->getName());
        $this->em->flush();
        $this->em->clear();

        $firstInitializer = $this->em->getRepository(Initializer1::class)->find(1);
        $this->assertInstanceOf(SubClass::class, $firstInitializer->getSub());
        $this->assertSame(2, $firstInitializer->getSub()->getId());
        $this->assertInstanceOf(Initializer1::class, $sub->getFirstInitializer());
        $this->assertSame(1, $sub->getFirstInitializer()->getId());

        $sub = $firstInitializer->getSub();
        $firstInitializer->setSub(null);
        $this->assertNull($firstInitializer->getSub());
        $this->assertNull($sub->getFirstInitializer());
        $this->em->flush();
        $this->em->clear();

        $firstInitializer = $this->em->getRepository(Initializer1::class)->find(1);
        $sub = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertNull($firstInitializer->getSub());
        $this->assertNull($sub->getFirstInitializer());

        $sub = $this->createSubClass(3);
        $firstInitializer->setSub($sub);
        $this->assertInstanceOf(SubClass::class, $firstInitializer->getSub());
        $this->assertSame(3, $firstInitializer->getSub()->getId());
        $this->assertInstanceOf(Initializer1::class, $sub->getFirstInitializer());
        $this->assertSame(1, $sub->getFirstInitializer()->getId());
        $this->em->flush();
        $this->em->clear();

        $firstInitializer = $this->em->getRepository(Initializer1::class)->find(1);
        $sub = $this->em->getRepository(SubClass::class)->find(3);
        $this->assertInstanceOf(SubClass::class, $firstInitializer->getSub());
        $this->assertSame(3, $firstInitializer->getSub()->getId());
        $this->assertInstanceOf(Initializer1::class, $sub->getFirstInitializer());
        $this->assertSame(1, $sub->getFirstInitializer()->getId());
    }

    public function testAssociationOneToOneOwning(): void
    {
        $firstInitializer = $this->createInitializer1(1);
        $sub = $this->createSubClass(2, $firstInitializer);
        $this->assertInstanceOf(Initializer1::class, $sub->getFirstInitializer());
        $this->assertSame('Initializer1 name 1', $sub->getFirstInitializer()->getName());
        // $firstInitializer is not tested (because reverse side)
        $this->em->flush();
        $this->em->clear();

        $sub = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertInstanceOf(Initializer1::class, $sub->getFirstInitializer());
        $this->assertSame(1, $sub->getFirstInitializer()->getId());
        // $firstInitializer is not tested (because reverse side)

        $sub->setFirstInitializer(null);
        $this->assertNull($sub->getFirstInitializer());
        // Initializer1 is not tested (because reverse side)
        $this->em->flush();
        $this->em->clear();

        $sub = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertNull($sub->getFirstInitializer());
        // Initializer1 is not tested (because reverse side)

        $firstInitializer = $this->createInitializer1(2);
        $this->em->persist($firstInitializer);
        $sub->setFirstInitializer($firstInitializer);
        $this->assertInstanceOf(Initializer1::class, $sub->getFirstInitializer());
        $this->assertSame('Initializer1 name 2', $sub->getFirstInitializer()->getName());
        // Initializer1 is not tested (because reverse side)
        $this->em->flush();
        $this->em->clear();

        $sub = $this->em->getRepository(SubClass::class)->find(2);
        $this->assertInstanceOf(Initializer1::class, $sub->getFirstInitializer());
        $this->assertSame('Initializer1 name 2', $sub->getFirstInitializer()->getName());
        // Initializer1 is not tested (because reverse side)
    }

    public function testAssociationOneToManyReverse(): void
    {
        $book = $this->createBook(1);
        $category = $this->createCategory(2, $book);
        $this->assertInstanceOf(Collection::class, $category->getBooks());
        $this->assertCount(1, $category->getBooks());
        $this->assertSame('Title 1', $category->getBooks()->first()->getTitle());
        $this->assertTrue($category->getBooks()->contains($book));
        $this->assertInstanceOf(Category::class, $book->getCategory());
        $this->assertSame(2, $book->getCategory()->getCategoryId());
        $this->em->flush();
        $this->em->clear();

        $category = $this->em->getRepository(Category::class)->find(2);
        $book = $this->em->getRepository(Book::class)->find(1);
        $this->assertInstanceOf(Collection::class, $category->getBooks());
        $this->assertCount(1, $category->getBooks());
        $this->assertSame(1, $category->getBooks()->first()->getBookId());
        $this->assertTrue($category->getBooks()->contains($book));
        $this->assertInstanceOf(Category::class, $book->getCategory());
        $this->assertSame(2, $book->getCategory()->getCategoryId());

        $category->removeBook($book);
        $this->assertInstanceOf(Collection::class, $category->getBooks());
        $this->assertCount(0, $category->getBooks());
        $this->assertNull($book->getCategory());
        $this->em->flush();
        $this->em->clear();

        $category = $this->em->getRepository(Category::class)->find(2);
        $book1 = $this->em->getRepository(Book::class)->find(1);
        $this->assertInstanceOf(Collection::class, $category->getBooks());
        $this->assertCount(0, $category->getBooks());
        $this->assertNull($book1->getCategory());

        $book2 = $this->createBook(2);
        $category->addBook($book1);
        $category->addBook($book2);
        $this->assertInstanceOf(Collection::class, $category->getBooks());
        $this->assertCount(2, $category->getBooks());
        $this->assertTrue($category->getBooks()->contains($book1));
        $this->assertTrue($category->getBooks()->contains($book2));
        $this->assertSame(2, $book1->getCategory()->getCategoryId());
        $this->assertSame(2, $book2->getCategory()->getCategoryId());
        $this->em->flush();
        $this->em->clear();

        $category = $this->em->getRepository(Category::class)->find(2);
        $book1 = $this->em->getRepository(Book::class)->find(1);
        $book2 = $this->em->getRepository(Book::class)->find(2);
        $this->assertInstanceOf(Collection::class, $category->getBooks());
        $this->assertCount(2, $category->getBooks());
        $this->assertTrue($category->getBooks()->contains($book1));
        $this->assertTrue($category->getBooks()->contains($book2));
        $this->assertSame(2, $book1->getCategory()->getCategoryId());
        $this->assertSame(2, $book2->getCategory()->getCategoryId());
    }

    public function testAssociationManyToManyUnidirectional(): void
    {
        $author = $this->createAuthor(5);
        $initializer = $this->createInitializer2($author);
        $this->assertInstanceOf(Collection::class, $initializer->getAuthors());
        $this->assertCount(1, $initializer->getAuthors());
        $this->assertSame(5, $initializer->getAuthors()->first()->getAuthorId());
        $this->assertTrue($initializer->getAuthors()->contains($author));
        $this->em->flush();
        $this->em->clear();

        $initializer = $this->em->getRepository(Initializer2::class)->find(1);
        $author = $this->em->getRepository(Author::class)->find(5);
        $this->assertInstanceOf(Collection::class, $initializer->getAuthors());
        $this->assertCount(1, $initializer->getAuthors());
        $this->assertSame(5, $initializer->getAuthors()->first()->getAuthorId());
        $this->assertTrue($initializer->getAuthors()->contains($author));

        $initializer->removeAuthor($author);
        $this->assertInstanceOf(Collection::class, $initializer->getAuthors());
        $this->assertCount(0, $initializer->getAuthors());
        $this->em->flush();
        $this->em->clear();

        $initializer = $this->em->getRepository(Initializer2::class)->find(1);
        $this->assertInstanceOf(Collection::class, $initializer->getAuthors());
        $this->assertCount(0, $initializer->getAuthors());

        $author1 = $this->em->getRepository(Author::class)->find(5);
        $author2 = $this->createAuthor(6);
        $initializer->addAuthor($author1);
        $initializer->addAuthor($author2);
        $this->assertInstanceOf(Collection::class, $initializer->getAuthors());
        $this->assertCount(2, $initializer->getAuthors());
        $this->assertTrue($initializer->getAuthors()->contains($author1));
        $this->assertTrue($initializer->getAuthors()->contains($author2));
        $this->em->flush();
        $this->em->clear();

        $initializer = $this->em->getRepository(Initializer2::class)->find(1);
        $author1 = $this->em->getRepository(Author::class)->find(5);
        $author2 = $this->em->getRepository(Author::class)->find(6);
        $this->assertInstanceOf(Collection::class, $initializer->getAuthors());
        $this->assertCount(2, $initializer->getAuthors());
        $this->assertTrue($initializer->getAuthors()->contains($author1));
        $this->assertTrue($initializer->getAuthors()->contains($author2));
    }

    public function testAssociationManyToManyReverse(): void
    {
        $book = $this->createBook(1);
        $author = $this->createAuthor(5, $book);
        $this->assertInstanceOf(Collection::class, $author->getBooks());
        $this->assertCount(1, $author->getBooks());
        $this->assertSame('Title 1', $author->getBooks()->first()->getTitle());
        $this->assertTrue($author->getBooks()->contains($book));
        $this->assertInstanceOf(Collection::class, $book->getAuthors());
        $this->assertCount(1, $book->getAuthors());
        $this->assertSame(5, $book->getAuthors()->first()->getAuthorId());
        $this->assertTrue($book->getAuthors()->contains($author));
        $this->em->flush();
        $this->em->clear();

        $author = $this->em->getRepository(Author::class)->find(5);
        $book = $this->em->getRepository(Book::class)->find(1);
        $this->assertInstanceOf(Collection::class, $author->getBooks());
        $this->assertCount(1, $author->getBooks());
        $this->assertSame(1, $author->getBooks()->first()->getBookId());
        $this->assertTrue($author->getBooks()->contains($book));
        $this->assertInstanceOf(Collection::class, $book->getAuthors());
        $this->assertCount(1, $book->getAuthors());
        $this->assertSame(5, $book->getAuthors()->first()->getAuthorId());
        $this->assertTrue($book->getAuthors()->contains($author));

        $author->removeBook($book);
        $this->assertInstanceOf(Collection::class, $author->getBooks());
        $this->assertCount(0, $author->getBooks());
        $this->assertInstanceOf(Collection::class, $book->getAuthors());
        $this->assertCount(0, $book->getAuthors());
        $this->em->flush();
        $this->em->clear();

        $author = $this->em->getRepository(Author::class)->find(5);
        $book1 = $this->em->getRepository(Book::class)->find(1);
        $this->assertInstanceOf(Collection::class, $author->getBooks());
        $this->assertCount(0, $author->getBooks());
        $this->assertInstanceOf(Collection::class, $book1->getAuthors());
        $this->assertCount(0, $book1->getAuthors());

        $book2 = $this->createBook(2);
        $author->addBook($book1);
        $author->addBook($book2);
        $this->assertInstanceOf(Collection::class, $author->getBooks());
        $this->assertCount(2, $author->getBooks());
        $this->assertTrue($author->getBooks()->contains($book1));
        $this->assertTrue($author->getBooks()->contains($book2));
        $this->assertInstanceOf(Collection::class, $book1->getAuthors());
        $this->assertCount(1, $book1->getAuthors());
        $this->assertTrue($book1->getAuthors()->contains($author));
        $this->assertInstanceOf(Collection::class, $book2->getAuthors());
        $this->assertCount(1, $book2->getAuthors());
        $this->assertTrue($book2->getAuthors()->contains($author));
        $this->em->flush();
        $this->em->clear();

        $author = $this->em->getRepository(Author::class)->find(5);
        $book1 = $this->em->getRepository(Book::class)->find(1);
        $book2 = $this->em->getRepository(Book::class)->find(2);
        $this->assertInstanceOf(Collection::class, $author->getBooks());
        $this->assertCount(2, $author->getBooks());
        $this->assertTrue($author->getBooks()->contains($book1));
        $this->assertTrue($author->getBooks()->contains($book2));
        $this->assertInstanceOf(Collection::class, $book1->getAuthors());
        $this->assertCount(1, $book1->getAuthors());
        $this->assertTrue($book1->getAuthors()->contains($author));
        $this->assertInstanceOf(Collection::class, $book2->getAuthors());
        $this->assertCount(1, $book2->getAuthors());
        $this->assertTrue($book2->getAuthors()->contains($author));
    }

    public function testAssociationManyToManyOwning(): void
    {
        $author = $this->createAuthor(5);
        $book = $this->createBook(1, null, $author);
        $this->assertInstanceOf(Collection::class, $book->getAuthors());
        $this->assertCount(1, $book->getAuthors());
        $this->assertSame(5, $book->getAuthors()->first()->getAuthorId());
        $this->assertTrue($book->getAuthors()->contains($author));
        // $author is not tested (because reverse side)
        $this->em->flush();
        $this->em->clear();

        $book = $this->em->getRepository(Book::class)->find(1);
        $author = $this->em->getRepository(Author::class)->find(5);
        $this->assertInstanceOf(Collection::class, $book->getAuthors());
        $this->assertCount(1, $book->getAuthors());
        $this->assertSame(5, $book->getAuthors()->first()->getAuthorId());
        $this->assertTrue($book->getAuthors()->contains($author));
        // $author is not tested (because reverse side)

        $author->removeBook($book);
        $this->assertInstanceOf(Collection::class, $book->getAuthors());
        $this->assertCount(0, $book->getAuthors());
        // $author is not tested (because reverse side)
        $this->em->flush();
        $this->em->clear();

        $book = $this->em->getRepository(Book::class)->find(1);
        $author1 = $this->em->getRepository(Author::class)->find(5);
        $this->assertInstanceOf(Collection::class, $book->getAuthors());
        $this->assertCount(0, $book->getAuthors());
        // $author1 is not tested (because reverse side)

        $author2 = $this->createAuthor(6);
        $book->addAuthor($author1);
        $book->addAuthor($author2);
        $this->assertInstanceOf(Collection::class, $book->getAuthors());
        $this->assertCount(2, $book->getAuthors());
        $this->assertTrue($book->getAuthors()->contains($author1));
        $this->assertTrue($book->getAuthors()->contains($author2));
        // $author1 and $author2 are not tested (because reverse side)
        $this->em->flush();
        $this->em->clear();

        $book = $this->em->getRepository(Book::class)->find(1);
        $author1 = $this->em->getRepository(Author::class)->find(5);
        $author2 = $this->em->getRepository(Author::class)->find(6);
        $this->assertInstanceOf(Collection::class, $book->getAuthors());
        $this->assertCount(2, $book->getAuthors());
        $this->assertTrue($book->getAuthors()->contains($author1));
        $this->assertTrue($book->getAuthors()->contains($author2));
        // $author1 and $author2 are not tested (because reverse side)
    }

    protected function createSubClass(int $id, Initializer1 $firstInitializer = null, Initializer2 $secondInitializer = null): SubClass
    {
        $object = new MyObject();
        $object->hello = 'world '.$id;

        $subClass = new SubClass();
        $subClass->addValues($id);
        $subClass->setName('Name '.$id)
            ->setFirstInitializer($firstInitializer)
            ->setSecondInitializer($secondInitializer)
            ->setDecimalField(0.55) // Give a double - Doctrine will return string
            ->setDecimalFieldWithHint('0.65')
            ->setDateField(new \DateTime('2020-01-01 00:00:00'))
            ->setBooleanField(true)
            ->setTextField('Text '.$id)
            ->setObjectField($object)
            ->setArrayField(['a' => $id])
            ->setSimpleArrayField(['b' => $id])
            ->setJsonField(['c' => $id])
            ->setGuidField('GUID '.$id)
            ->setCustomField('Custom '.$id);
        $this->em->persist($subClass);

        return $subClass;
    }

    protected function createInitializer1(int $suffix, SubClass $sub = null): Initializer1
    {
        $firstInitializer = new Initializer1();
        $firstInitializer->setName('Initializer1 name '.$suffix);
        if ($sub) {
            $firstInitializer->setSub($sub);
        }
        $this->em->persist($firstInitializer);

        return $firstInitializer;
    }

    protected function createInitializer2(Author $author = null): Initializer2
    {
        $secondInitializer = new Initializer2();
        if ($author) {
            $secondInitializer->addAuthor($author);
        }
        $this->em->persist($secondInitializer);

        return $secondInitializer;
    }

    protected function createBook(int $suffix, Category $category = null, Author $author = null): Book
    {
        $book = new Book();
        $book->setTitle('Title '.$suffix)
            ->definePrice(10.5);
        if ($category) {
            $book->setCategory($category);
        }
        if ($author) {
            $book->addAuthor($author);
        }
        $this->em->persist($book);

        return $book;
    }

    protected function createCategory(int $id, Book $book = null): Category
    {
        $category = new Category();
        $category->setCategoryId($id)
            ->setName('Name '.$id)
            ->setCustomField('Custom '.$id);
        if ($book) {
            $category->addBook($book);
        }
        $this->em->persist($category);

        return $category;
    }

    protected function createAuthor(int $id, Book $book = null): Author
    {
        $author = new Author();
        $author->setAuthorId($id)
            ->setFirstName('First name '.$id)
            ->setLastName('Last name '.$id)
            ->phoneNumber = 'PHONE';
        if ($book) {
            $author->addBook($book);
        }
        $this->em->persist($author);

        return $author;
    }
}
