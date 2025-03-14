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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\Command;

use Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\EntityGeneratorInterface;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher\EntitySearcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'ecommit:doctrine:generate-entities', description: 'Generate Doctrine ORM entities')]
class GenerateEntitiesCommand extends Command
{
    public function __construct(protected EntitySearcherInterface $searcher, protected EntityGeneratorInterface $generator)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('class', InputArgument::REQUIRED, 'Class name or class name prefix')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $classes = $this->searcher->search($input->getArgument('class'));
        if (0 === \count($classes)) {
            throw new \Exception('No class found');
        }

        foreach ($classes as $class) {
            $this->generator->generate($class);

            $output->writeln(\sprintf('-> Class "%s" has been generated.', $class));
        }

        return 0;
    }
}
