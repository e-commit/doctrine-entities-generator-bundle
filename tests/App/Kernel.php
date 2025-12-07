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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Ecommit\DoctrineEntitiesGeneratorBundle\EcommitDoctrineEntitiesGeneratorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/framework.yaml');
        $loader->load(__DIR__.'/config/doctrine.yaml');

        $loader->load(function (ContainerBuilder $container): void {
            $container->setParameter('entity_dir', $this->getEntityDir());
            $container->setParameter('entity_prefix', $this->getEntityPrefix());

            if (\PHP_VERSION_ID >= 80400) { // @legacy
                $container->loadFromExtension('doctrine', [
                    'orm' => [
                        'enable_native_lazy_objects' => true,
                    ],
                ]);
            } else {
                $container->loadFromExtension('doctrine', [
                    'orm' => [
                        'auto_generate_proxy_classes' => false,
                    ],
                ]);
            }
        });
    }

    public function getEntityDir(): string
    {
        return $this->getProjectDir().'/Entity';
    }

    public function getEntityPrefix(): string
    {
        return 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity';
    }

    public function registerBundles(): iterable
    {
        return [
            new TwigBundle(),
            new DoctrineBundle(),
            new FrameworkBundle(),
            new EcommitDoctrineEntitiesGeneratorBundle(),
        ];
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }
}
