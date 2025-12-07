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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @phpstan-type ProcessedConfiguration array{
 *     template: string
 * }
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ecommit_doctrine_entities_generator');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('template')
                    ->defaultValue('@EcommitDoctrineEntitiesGenerator/Theme/base.php.twig')
                    ->validate()
                        ->ifTrue(fn (mixed $value) => !\is_string($value))
                        ->thenInvalid('Invalid template')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
