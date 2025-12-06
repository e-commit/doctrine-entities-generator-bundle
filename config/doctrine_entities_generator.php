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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\Persistence\ManagerRegistry;
use Ecommit\DoctrineEntitiesGeneratorBundle\Command\GenerateEntitiesCommand;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\EntityGenerator;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher\EntitySearcher;
use Twig\Environment;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('ecommit_doctrine_entities_generator.entity_generator', EntityGenerator::class)
        ->args([
            service('ecommit_doctrine_entities_generator.entity_searcher'),
            service(ManagerRegistry::class),
            service(Environment::class),
            param('ecommit_doctrine_entities_generator.template'),
        ])
        ->alias(EntityGenerator::class, 'ecommit_doctrine_entities_generator.entity_generator')

        ->set('ecommit_doctrine_entities_generator.entity_searcher', EntitySearcher::class)
        ->private()
        ->args([service(ManagerRegistry::class)])
        ->alias(EntitySearcher::class, 'ecommit_doctrine_entities_generator.entity_searcher')->private()

        ->set('ecommit_doctrine_entities_generator.command.generate_entities', GenerateEntitiesCommand::class)
        ->private()
        ->args([
            service('ecommit_doctrine_entities_generator.entity_searcher'),
            service('ecommit_doctrine_entities_generator.entity_generator'),
        ])
        ->tag('console.command')
    ;
};
