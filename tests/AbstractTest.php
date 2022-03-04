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
                $this->getContainer()->get(EntitySearcher::class),
                $this->getContainer()->get(ManagerRegistry::class),
                $this->getContainer()->get(Environment::class),
                $this->getContainer()->getParameter('ecommit_doctrine_entities_generator.template'),
            ])
            ->onlyMethods(['writeFile'])
            ->getMock();

        $mock->method('writeFile')->willReturnCallback(function (\ReflectionClass $reflectionClass, string $content): void {
            $filename = $this->tempFolder.'/'.str_replace('/', '_', $this->getClassSubPath($reflectionClass->getName())).'.php';
            file_put_contents($filename, $content);
        });

        return $mock;
    }

    protected function checkGeneratedClass($class): void
    {
        $className = $this->getClassSubPath($class);

        $file = $this->tempFolder.'/'.str_replace('/', '_', $className).'.php';
        $expectedContent = null;
        foreach (['GeneratedEntity'.\PHP_MAJOR_VERSION, 'GeneratedEntity'] as $generatedFolder) {
            $expectedFile = __DIR__.'/App/'.$generatedFolder.'/'.$className.'.php';
            if (!file_exists($expectedFile)) {
                continue;
            }

            $expectedContent = file_get_contents($expectedFile);
            $expectedContent = str_replace(
                'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\\'.$generatedFolder,
                'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity',
                $expectedContent
            );

            break;
        }
        if (null === $expectedContent) {
            throw new \Exception('Template not found: '.$className);
        }

        $this->assertSame(
            $expectedContent,
            file_get_contents($file)
        );
    }

    protected function getClassSubPath($class): string
    {
        $className = str_replace('Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\\', '', $class);
        $className = str_replace('\\', '/', $className);

        return $className;
    }
}
