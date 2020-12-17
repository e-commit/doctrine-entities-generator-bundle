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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\EntityGenerator\Util;

use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\Util\UseStatementManipulator;
use PHPUnit\Framework\TestCase;

class UseStatementManipulatorTest extends TestCase
{
    public function testAddUseStatementIfNecessarySameNamespace(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('MySecondClass', $manipulator->addUseStatementIfNecessary('Foo\Bar\MySecondClass'));
        $this->assertSame($sourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryNamespaceNotFound(): void
    {
        $sourceCode = <<<'CODE'
<?php
class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Could not find namespace node');
        $manipulator->addUseStatementIfNecessary('Foo\Bar\MySecondClass');
    }

    public function testAddUseStatementIfNecessaryAlreadyExist(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub\MySecondClass;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('MySecondClass', $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\MySecondClass'));
        $this->assertSame($sourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryAliasAlreadyExist(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub\MySecondClass as MyAlias;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('MyAlias', $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\MySecondClass'));
        $this->assertSame($sourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryConflict(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Other\MySecondClass;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('\Foo\Bar\Sub\MySecondClass', $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\MySecondClass'));
        $this->assertSame($sourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryAliasConflict(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Other\MySecondClass as Hello;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('\Foo\Bar\Sub\Hello', $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\Hello'));
        $this->assertSame($sourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryBeforeAlphabetical(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub\Apple;
use Foo\Bar\Sub\Tomato;

class MyClass
{
}
CODE;
        $expectedSourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub\Apple;
use Foo\Bar\Sub\Bean;
use Foo\Bar\Sub\Tomato;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('Bean', $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\Bean'));
        $this->assertSame($expectedSourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryBottom(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub\Apple;
use Foo\Bar\Sub\Bean;

class MyClass
{
}
CODE;
        $expectedSourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub\Apple;
use Foo\Bar\Sub\Bean;
use Foo\Bar\Sub\Tomato;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('Tomato', $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\Tomato'));
        $this->assertSame($expectedSourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryBadOrder(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub\Tomato;
use Foo\Bar\Sub\Apple;

class MyClass
{
}
CODE;
        $expectedSourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub\Bean;
use Foo\Bar\Sub\Tomato;
use Foo\Bar\Sub\Apple;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('Bean', $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\Bean'));
        $this->assertSame($expectedSourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryOnyOneUse(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

class MyClass
{
}
CODE;
        $expectedSourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub\Bean;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('Bean', $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\Bean'));
        $this->assertSame($expectedSourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryWithUseNamespace(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub as MySub;

class MyClass
{
}
CODE;
        $expectedSourceCode = <<<'CODE'
<?php
namespace Foo\Bar;

use Foo\Bar\Sub as MySub;
use Foo\Bar\Sub\Bean;

class MyClass
{
}
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->assertSame('Bean', $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\Bean'));
        $this->assertSame($expectedSourceCode, $manipulator->getSourceCode());
    }

    public function testAddUseStatementIfNecessaryClassNotFound(): void
    {
        $sourceCode = <<<'CODE'
<?php
namespace Foo\Bar;
CODE;
        $manipulator = new UseStatementManipulator($sourceCode);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Could not find a class!');
        $manipulator->addUseStatementIfNecessary('Foo\Bar\Sub\Bean');
    }

    /**
     * @dataProvider getTestGetNamespaceProvider
     */
    public function testGetNamespace(string $class, string $namespace): void
    {
        $this->assertSame($namespace, UseStatementManipulator::getNamespace($class));
    }

    public function getTestGetNamespaceProvider(): array
    {
        return [
            ['Foo\Bar\MyClass', 'Foo\Bar'],
            ['Foo\Bar', 'Foo'],
            ['Foo', ''],
        ];
    }

    /**
     * @dataProvider getTestGetShortClassNameProvider
     */
    public function testGetShortClassName(string $class, string $shortClass): void
    {
        $this->assertSame($shortClass, UseStatementManipulator::getShortClassName($class));
    }

    public function getTestGetShortClassNameProvider(): array
    {
        return [
            ['Foo\Bar\MyClass', 'MyClass'],
            ['Foo\Bar', 'Bar'],
            ['Foo', 'Foo'],
        ];
    }

    /**
     * @dataProvider getTestAreClassesAlphabeticalProvider
     */
    public function testAreClassesAlphabetical(string $class1, string $class2, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, UseStatementManipulator::areClassesAlphabetical($class1, $class2));
    }

    public function getTestAreClassesAlphabeticalProvider(): array
    {
        return [
            ['Bar\Foo', 'Foo\Bar', true],
            ['Foo\Bar', 'Bar\Foo', false],
            ['Foo\Bar\MyClass', 'Foo\Bar\MySecondClass', true],
            ['Foo\Bar\MySecondClass', 'Foo\Bar\MyClass', false],
        ];
    }
}
