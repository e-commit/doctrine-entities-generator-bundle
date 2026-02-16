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

use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\Util\PhpDocPropertyTypeExtractor;
use Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\WithPhpDoc;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PhpDocPropertyTypeExtractorTest extends TestCase
{
    /**
     * @param class-string $class
     */
    #[DataProvider('getGetPropertyPhpDocVarTypeProvider')]
    public function testGetPropertyPhpDocVarType(string $class, string $property, ?string $expected): void
    {
        $reflectionMethod = new \ReflectionProperty($class, $property);
        $this->assertSame($expected, (new PhpDocPropertyTypeExtractor())->getPropertyPhpDocVarType($reflectionMethod));
    }

    public static function getGetPropertyPhpDocVarTypeProvider(): array
    {
        return [
            [WithPhpDoc::class, 'withoutPhpDoc', null],
            [WithPhpDoc::class, 'withPositiveIntPhpDoc', 'positive-int'],
            [WithPhpDoc::class, 'withMultipleTypesPhpDoc', '(int<0, 10> | int<100, 110> | null)'],
            [WithPhpDoc::class, 'withInlinePhpDoc', 'positive-int'],
            [WithPhpDoc::class, 'withCommentPhpDoc', 'positive-int'],
            [WithPhpDoc::class, 'withOnlyCommentPhpDoc', null],
            [WithPhpDoc::class, 'withPhpstanTypePhpDoc', 'MyType'],
            [WithPhpDoc::class, 'withPhpstanImportTypePhpDoc', 'Person'],
            [WithPhpDoc::class, 'withPhpstanImportTypeAliasPhpDoc', 'MyAddress'],
            [WithPhpDoc::class, 'withArrayShapePhpDoc', 'array{first_name: string, last_name: string, address?: MyAddress}'],
            [WithPhpDoc::class, 'withArrayShapeTypePhpDoc', 'MyArrayShape'],
        ];
    }
}
