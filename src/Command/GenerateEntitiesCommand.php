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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntitiesCommand extends Command
{
    /**
     * @var EntitySearcherInterface
     */
    protected $searcher;

    /**
     * @var EntityGeneratorInterface
     */
    protected $generator;

    protected static $defaultName = 'ecommit:doctrine:generate-entities';

    protected static $defaultDescription = 'Generate Doctrine ORM entities';

    public function __construct(EntitySearcherInterface $searcher, EntityGeneratorInterface $generator)
    {
        $this->searcher = $searcher;
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('class', InputArgument::REQUIRED, 'Class name or class name prefix')
            ->setDescription(self::$defaultDescription) //Compatibility with Symfony < 5.3
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classes = $this->searcher->search($input->getArgument('class'));
        if (0 === \count($classes)) {
            throw new \Exception('No class found');
        }

        foreach ($classes as $class) {
            $this->generator->generate($class);

            $output->writeln(sprintf('-> Class "%s" has been generated.', $class));
        }

        return 0;
    }
}
