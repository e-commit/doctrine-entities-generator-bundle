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

use Doctrine\Persistence\ManagerRegistry;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\EntityGenerator;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher\EntitySearcher;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

abstract class AbstractTest extends KernelTestCase
{
    protected $tempFolder;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->tempFolder = self::$kernel->getProjectDir().'/var/tmp';
        $fs = new Filesystem();
        if ($fs->exists($this->tempFolder)) {
            $fs->remove($this->tempFolder);
        }
        $fs->mkdir($this->tempFolder);
    }

    protected function tearDown(): void
    {
        $fs = new Filesystem();
        if ($fs->exists($this->tempFolder)) {
            $fs->remove($this->tempFolder);
        }

        parent::tearDown();
    }

    protected function getEntityGeneratorMock(): MockObject
    {
        $mock = $this->getMockBuilder(EntityGenerator::class)
            ->setConstructorArgs([
                self::$container->get(EntitySearcher::class),
                self::$container->get(ManagerRegistry::class),
                self::$container->get(Environment::class),
                self::$container->getParameter('ecommit_doctrine_entities_generator.template'),
            ])
            ->onlyMethods(['writeFile'])
            ->getMock();

        $mock->method('writeFile')->willReturnCallback(function (\ReflectionClass $reflectionClass, string $content): void {
            $filename = $this->tempFolder.'/'.$reflectionClass->getShortName().'.php';
            file_put_contents($filename, $content);
        });

        return $mock;
    }

    protected function checkGeneratedClass($class): void
    {
        $reflectionClass = new \ReflectionClass($class);

        $file = $this->tempFolder.'/'.$reflectionClass->getShortName().'.php';
        $expectedFile = __DIR__.'/App/GeneratedEntity/'.$reflectionClass->getShortName().'.php';
        $expectedContent = file_get_contents($expectedFile);
        $expectedContent = str_replace(
            'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\GeneratedEntity',
            'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity',
            $expectedContent
        );

        $this->assertSame(
            $expectedContent,
            file_get_contents($file)
        );
    }
}
