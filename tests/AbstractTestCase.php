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

abstract class AbstractTestCase extends KernelTestCase
{
    protected string $tempFolder;

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

    /**
     * @return MockObject&EntityGenerator
     */
    protected function getEntityGeneratorMock()
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

    protected function checkGeneratedClass(string $class, ?string $folder = null): void
    {
        $className = $this->getClassSubPath($class);
        $file = $this->tempFolder.'/'.str_replace('/', '_', $className).'.php';
        if (null === $folder) {
            $folder = 'GeneratedEntity';
        }

        $expectedFile = __DIR__.'/App/'.$folder.'/'.$className.'.php';
        if (!file_exists($expectedFile)) {
            throw new \Exception('Template not found: '.$className);
        }

        $expectedContent = (string) file_get_contents($expectedFile);
        $expectedContent = str_replace(
            'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\\'.$folder,
            'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity',
            $expectedContent
        );
        $expectedContent = str_replace(
            'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\\GeneratedEntity',
            'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity',
            $expectedContent
        );

        $this->assertSame(
            $expectedContent,
            file_get_contents($file)
        );
    }

    protected function getClassSubPath(string $class): string
    {
        $className = str_replace('Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\\', '', $class);
        $className = str_replace('\\', '/', $className);

        return $className;
    }
}
