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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\Command;

use Ecommit\DoctrineEntitiesGeneratorBundle\Command\GenerateEntitiesCommand;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher\EntitySearcher;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\AbstractTest;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Sale;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\Finder;

class GenerateEntitiesCommandTest extends AbstractTest
{
    public function testExecute(): void
    {
        $commandTester = $this->createCommandTester();
        $exitCode = $commandTester->execute([
            'class' => 'Ecommit/DoctrineEntitiesGeneratorBundle/Tests/App/Entity/S*',
        ]);

        $expectedResult[] = '-> Class "Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Sale" has been generated.';
        $expectedResult[] = '-> Class "Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\SubClass" has been generated.';

        $this->assertSame(0, $exitCode);
        $this->assertSame(implode("\n", $expectedResult)."\n", $commandTester->getDisplay());

        $this->checkGeneratedClass(Sale::class);
        $this->checkGeneratedClass(SubClass::class);

        $finder = new Finder();
        $finder->files()->in($this->tempFolder);
        $this->assertCount(2, $finder);
    }

    public function testNoResult(): void
    {
        $commandTester = $this->createCommandTester();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No class found');

        $commandTester->execute([
            'class' => 'BadValue',
        ]);
    }

    protected function createCommandTester(): CommandTester
    {
        $application = new Application();

        $command = new GenerateEntitiesCommand(
            $this->getContainer()->get(EntitySearcher::class),
            $this->getEntityGeneratorMock()
        );

        $application->add($command);

        return new CommandTester($application->find('ecommit:doctrine:generate-entities'));
    }
}
