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

namespace Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ManagerRegistry;
use Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\GenerateEntityTemplate;
use Ecommit\DoctrineEntitiesGeneratorBundle\Entity\EntityInitializerInterface;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher\EntitySearcherInterface;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\ClassNotManagedException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\EntityInitializerInterfaceNotUsedException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\TagNotFoundException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Model\GenerateEntityRequest;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Twig\Environment;

/**
 * @phpstan-import-type FieldMapping from ClassMetadataInfo
 * @phpstan-import-type EmbeddedClassMapping from ClassMetadataInfo
 * @phpstan-import-type AssociationMapping from ClassMetadataInfo
 * @phpstan-import-type FileParts from GenerateEntityRequest
 */
class EntityGenerator implements EntityGeneratorInterface
{
    public const TYPE_GET = 'get';
    public const TYPE_SET = 'set';
    public const TYPE_ADD = 'add';
    public const TYPE_REMOVE = 'remove';

    /**
     * @var EntitySearcherInterface
     */
    protected $searcher;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var Inflector
     */
    protected $inflector;

    /**
     * @var string
     */
    protected $template;

    public function __construct(EntitySearcherInterface $searcher, ManagerRegistry $registry, Environment $twig, string $template)
    {
        $this->searcher = $searcher;
        $this->registry = $registry;
        $this->twig = $twig;
        $this->template = $template;
        $this->inflector = InflectorFactory::create()->build();
    }

    public function generate(string $className): void
    {
        $reflectionClass = new \ReflectionClass($className);
        [$entityManager, $metadata] = $this->getManagerAndMetadata($reflectionClass);

        if (!$this->searcher->classCanBeGenerated($metadata)) {
            throw new ClassNotManagedException(\sprintf('Class "%s" cannot be generated (Is IgnoreGenerateEntity attribute used ?)', $className));
        }

        $fileParts = $this->getFileParts($reflectionClass);
        $doctrineExtractor = new DoctrineExtractor($entityManager);
        $request = new GenerateEntityRequest(
            $reflectionClass,
            $fileParts,
            $metadata,
            $doctrineExtractor
        );

        $properties = $doctrineExtractor->getProperties($className);
        if (null === $properties) {
            $properties = [];
        }
        foreach ($properties as $property) {
            if (!$this->propertyIsDefinedInClassFile($request, $property)) {
                continue;
            }

            if ($metadata->hasField($property)) {
                if (\array_key_exists($property, $metadata->fieldMappings)) {
                    $this->addField($request, $metadata->fieldMappings[$property]);
                } else {
                    $this->addEmbedded($request, $property, $metadata->embeddedClasses[$property]);
                }
            } elseif ($metadata->hasAssociation($property)) {
                $this->addAssociation($request, $metadata->associationMappings[$property]);
            }
        }

        if (is_subclass_of($className, EntityInitializerInterface::class)) {
            $request->addInitializeEntity = true;
        }

        if (\count($request->newConstructorLines) > 0 || $request->addInitializeEntity) {
            if ($this->methodIsDefinedOutsideBlock($request, '__construct')) {
                throw new EntityInitializerInterfaceNotUsedException(\sprintf('Class "%s": __construct method is used. Remove it and implement "%s" interface', $className, EntityInitializerInterface::class));
            }

            array_unshift($request->newBlockContents, $this->renderBlock($reflectionClass, 'constructor', [
                'request' => $request,
            ]));
        }

        $newBlockContent = $this->renderBlock($reflectionClass, 'block_content', [
            'request' => $request,
        ]);

        /** @var string $content */
        $content = preg_replace($this->getPattern($reflectionClass), \sprintf('$1$2%s$4$5', $newBlockContent), $request->getSourceCode());
        $this->writeFile($reflectionClass, $content);
    }

    /**
     * @phpstan-template T of object
     *
     * @param \ReflectionClass<T> $reflectionClass
     *
     * @return array{0: EntityManagerInterface, 1: ClassMetadataInfo<T>}
     */
    protected function getManagerAndMetadata(\ReflectionClass $reflectionClass): array
    {
        $entityManager = $this->registry->getManagerForClass($reflectionClass->getName());
        if ($entityManager instanceof EntityManagerInterface) {
            $metadata = $entityManager->getClassMetadata($reflectionClass->getName());
            if (!$metadata instanceof ClassMetadataInfo) {
                throw new ClassNotManagedException(\sprintf('Class "%s" cannot be generated (Metatada not found)', $reflectionClass->getName()));
            }

            return [$entityManager, $metadata];
        }

        // Search embedded
        foreach ($this->registry->getManagers() as $entityManager) {
            if (!$entityManager instanceof EntityManagerInterface) {
                continue;
            }
            $metadataFactory = $entityManager->getMetadataFactory();
            foreach ($metadataFactory->getAllMetadata() as $metadata) {
                foreach ($metadata->embeddedClasses as $embedded) {
                    if ($embedded['class'] === $reflectionClass->getName()) {
                        $embeddedMetadata = $entityManager->getClassMetadata($embedded['class']);
                        if (!$embeddedMetadata instanceof ClassMetadataInfo) {
                            throw new ClassNotManagedException(\sprintf('Class "%s" cannot be generated (Metatada not found)', $reflectionClass->getName()));
                        }

                        return [$entityManager, $embeddedMetadata];
                    }
                }
            }
        }

        throw new ClassNotManagedException(\sprintf('Class "%s" not managed', $reflectionClass->getName()));
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return FileParts
     */
    protected function getFileParts(\ReflectionClass $reflectionClass): array
    {
        /** @var string $content */
        $content = file_get_contents((string) $reflectionClass->getFileName());
        $pattern = $this->getPattern($reflectionClass);
        if (!preg_match($pattern, $content, $fileParts)) {
            throw new TagNotFoundException(\sprintf('Class "%s": Start tag or end tag is not found', $reflectionClass->getName()));
        }
        /** @var FileParts $fileParts */
        $fileParts = $fileParts; // @phpstan-ignore-line

        return $fileParts;
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     */
    protected function getPattern(\ReflectionClass $reflectionClass): string
    {
        $startTag = $this->renderBlock($reflectionClass, 'start_tag');
        $endTag = $this->renderBlock($reflectionClass, 'end_tag');

        return \sprintf(
            '/^(?P<beforeBlock>.*)(?P<startTag>%s)(?P<block>.*)(?P<endTag>%s)(?P<afterBlock>.*)/is',
            preg_quote($startTag, '/'),
            preg_quote($endTag, '/')
        );
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     * @param array<mixed>             $parameters
     */
    protected function renderBlock(\ReflectionClass $reflectionClass, string $blockName, array $parameters = []): string
    {
        $templateName = $this->template;
        if ($attribute = $reflectionClass->getAttributes(GenerateEntityTemplate::class)[0] ?? null) {
            $templateName = $attribute->newInstance()->template;
        }

        $template = $this->twig->load($templateName);

        ob_start();

        $template->displayBlock($blockName, $parameters);

        $content = (string) ob_get_clean();

        if ("\n" === $content[0]) {
            $content = mb_substr($content, 1);
        }

        $len = mb_strlen($content);
        if ($len > 0 && "\n" === $content[$len - 1]) {
            $content = mb_substr($content, 0, $len - 1);
        }

        return $content;
    }

    protected function propertyIsDefinedInClassFile(GenerateEntityRequest $request, string $property): bool
    {
        $reflectionClass = $request->reflectionClass;
        try {
            $reflectionProperty = $reflectionClass->getProperty($property);
        } catch (\ReflectionException $e) {
            return false;
        }
        if ($reflectionProperty->getDeclaringClass()->getName() !== $reflectionClass->getName()) {
            return false;
        }
        if ($reflectionProperty->isPublic()) {
            return false;
        }

        foreach ($reflectionClass->getTraits() as $reflectionTrait) {
            if ($reflectionTrait->hasProperty($property)) {
                return false;
            }
        }

        return true;
    }

    protected function methodIsDefinedOutsideBlock(GenerateEntityRequest $request, string $method): bool
    {
        $reflectionClass = $request->reflectionClass;
        try {
            $reflectionMethod = $reflectionClass->getMethod($method);
        } catch (\ReflectionException $e) {
            return false;
        }

        if ($reflectionClass->getFileName() !== $reflectionMethod->getDeclaringClass()->getFileName()) {
            return true;
        }

        foreach ($reflectionClass->getTraits() as $reflectionTrait) {
            if ($reflectionTrait->hasMethod($method)) {
                return true;
            }
        }

        $endStartTagLine = mb_substr_count((string) $request->fileParts['beforeBlock'], \PHP_EOL) + mb_substr_count((string) $request->fileParts['startTag'], \PHP_EOL) + 1;
        $startLimit = $endStartTagLine + 1;
        if ($reflectionMethod->getStartLine() < $startLimit) {
            return true;
        }

        $startEndTagLine = $endStartTagLine + mb_substr_count((string) $request->fileParts['block'], \PHP_EOL);
        $endLimit = $startEndTagLine - 1;
        if ($reflectionMethod->getStartLine() > $endLimit) {
            return true;
        }

        return false;
    }

    /**
     * @param FieldMapping $fieldMapping
     */
    protected function addField(GenerateEntityRequest $request, array $fieldMapping): void
    {
        $fieldName = $fieldMapping['fieldName'];
        $types = $request->doctrineExtractor->getTypes($request->reflectionClass->getName(), $fieldName);
        $reflectionProperty = new \ReflectionProperty($request->reflectionClass->getName(), $fieldName);
        $phpType = $reflectionProperty->getType();

        if (null === $request->doctrineExtractor->isWritable($request->reflectionClass->getName(), $fieldName) && !$reflectionProperty->isReadOnly()) {
            $setMethodName = $this->buildMethodName(self::TYPE_SET, $fieldName);
            if (!$this->methodIsDefinedOutsideBlock($request, $setMethodName)) {
                $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, 'field_set', [
                    'methodName' => $setMethodName,
                    'fieldName' => $fieldName,
                    'variableName' => $this->buildVariableName(self::TYPE_SET, $fieldName),
                    'types' => $types,
                    'phpType' => $phpType,
                    'request' => $request,
                    'fieldMapping' => $fieldMapping,
                ]);
            }
        }

        $getMethodName = $this->buildMethodName(self::TYPE_GET, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $getMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, 'field_get', [
                'methodName' => $getMethodName,
                'fieldName' => $fieldName,
                'types' => $types,
                'phpType' => $phpType,
                'request' => $request,
                'fieldMapping' => $fieldMapping,
            ]);
        }
    }

    /**
     * @param EmbeddedClassMapping $embeddedMapping
     */
    protected function addEmbedded(GenerateEntityRequest $request, string $fieldName, array $embeddedMapping): void
    {
        $targetClass = $embeddedMapping['class'];
        $phpType = (new \ReflectionProperty($request->reflectionClass->getName(), $fieldName))->getType();

        $targetClassAlias = $request->useStatementManipulator->addUseStatementIfNecessary($targetClass);
        if ($request->reflectionClass->getName() === $targetClass) {
            $targetClassAlias = 'self';
        }

        $setMethodName = $this->buildMethodName(self::TYPE_SET, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $setMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, 'embedded_set', [
                'methodName' => $setMethodName,
                'fieldName' => $fieldName,
                'variableName' => $this->buildVariableName(self::TYPE_SET, $fieldName),
                'targetClass' => $targetClass,
                'targetClassAlias' => $targetClassAlias,
                'phpType' => $phpType,
                'request' => $request,
                'embeddedMapping' => $embeddedMapping,
            ]);
        }

        $getMethodName = $this->buildMethodName(self::TYPE_GET, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $getMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, 'embedded_get', [
                'methodName' => $getMethodName,
                'fieldName' => $fieldName,
                'targetClass' => $targetClass,
                'targetClassAlias' => $targetClassAlias,
                'phpType' => $phpType,
                'request' => $request,
                'embeddedMapping' => $embeddedMapping,
            ]);
        }
    }

    /**
     * @param AssociationMapping $associationMapping
     */
    protected function addAssociation(GenerateEntityRequest $request, array $associationMapping): void
    {
        if ($associationMapping['type'] & ClassMetadataInfo::TO_ONE) {
            if ($associationMapping['type'] & ClassMetadataInfo::ONE_TO_ONE && $associationMapping['mappedBy']) {
                $this->addAssociationToOne(
                    $request,
                    $associationMapping,
                    'assocation_one_to_one_reverse',
                    $this->buildMethodName(self::TYPE_SET, $associationMapping['mappedBy'])
                );
            } elseif ($associationMapping['type'] & ClassMetadataInfo::ONE_TO_ONE && $associationMapping['inversedBy']) {
                $this->addAssociationToOne(
                    $request,
                    $associationMapping,
                    'assocation_one_to_one_owning',
                    null
                );
            } elseif ($associationMapping['type'] & ClassMetadataInfo::ONE_TO_ONE) {
                $this->addAssociationToOne(
                    $request,
                    $associationMapping,
                    'assocation_one_to_one_unidirectional',
                    null
                );
            } elseif ($associationMapping['type'] & ClassMetadataInfo::MANY_TO_ONE && $associationMapping['inversedBy']) {
                $this->addAssociationToOne(
                    $request,
                    $associationMapping,
                    'assocation_many_to_one_owning',
                    null
                );
            } elseif ($associationMapping['type'] & ClassMetadataInfo::MANY_TO_ONE) {
                $this->addAssociationToOne(
                    $request,
                    $associationMapping,
                    'assocation_many_to_one_unidirectional',
                    null
                );
            }
        } elseif ($associationMapping['type'] & ClassMetadataInfo::TO_MANY) {
            if ($associationMapping['type'] & ClassMetadataInfo::ONE_TO_MANY && $associationMapping['mappedBy']) {
                $this->addAssociationToMany(
                    $request,
                    $associationMapping,
                    'assocation_one_to_many_reverse',
                    $this->buildMethodName(self::TYPE_SET, $associationMapping['mappedBy']),
                    $this->buildMethodName(self::TYPE_SET, $associationMapping['mappedBy'])
                );
            } elseif ($associationMapping['type'] & ClassMetadataInfo::MANY_TO_MANY && $associationMapping['mappedBy']) {
                $this->addAssociationToMany(
                    $request,
                    $associationMapping,
                    'assocation_many_to_many_reverse',
                    $this->buildMethodName(self::TYPE_ADD, $associationMapping['mappedBy']),
                    $this->buildMethodName(self::TYPE_REMOVE, $associationMapping['mappedBy'])
                );
            } elseif ($associationMapping['type'] & ClassMetadataInfo::MANY_TO_MANY && $associationMapping['inversedBy']) {
                $this->addAssociationToMany(
                    $request,
                    $associationMapping,
                    'assocation_many_to_many_owning',
                    null,
                    null
                );
            } elseif ($associationMapping['type'] & ClassMetadataInfo::MANY_TO_MANY) {
                $this->addAssociationToMany(
                    $request,
                    $associationMapping,
                    'assocation_many_to_many_unidirectional',
                    null,
                    null
                );
            }
        }
    }

    /**
     * @param AssociationMapping $associationMapping
     */
    protected function addAssociationToOne(GenerateEntityRequest $request, array $associationMapping, string $block, ?string $foreignMethodNameSet): void
    {
        $fieldName = $associationMapping['fieldName'];
        $targetEntity = $associationMapping['targetEntity'];

        $targetEntityAlias = $request->useStatementManipulator->addUseStatementIfNecessary($targetEntity);
        if ($request->reflectionClass->getName() === $targetEntity) {
            $targetEntityAlias = 'self';
        }

        $setMethodName = $this->buildMethodName(self::TYPE_SET, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $setMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, $block.'_set', [
                'methodName' => $setMethodName,
                'foreignMethodName' => $foreignMethodNameSet,
                'fieldName' => $fieldName,
                'variableName' => $this->buildVariableName(self::TYPE_SET, $fieldName),
                'targetEntity' => $targetEntity,
                'targetEntityAlias' => $targetEntityAlias,
                'request' => $request,
                'associationMapping' => $associationMapping,
            ]);
        }

        $getMethodName = $this->buildMethodName(self::TYPE_GET, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $getMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, 'assocation_to_one_get', [
                'methodName' => $getMethodName,
                'fieldName' => $fieldName,
                'targetEntity' => $targetEntity,
                'targetEntityAlias' => $targetEntityAlias,
                'request' => $request,
                'associationMapping' => $associationMapping,
            ]);
        }
    }

    /**
     * @param AssociationMapping $associationMapping
     */
    protected function addAssociationToMany(GenerateEntityRequest $request, array $associationMapping, string $block, ?string $foreignMethodNameAdd, ?string $foreignMethodNameRemove): void
    {
        $fieldName = $associationMapping['fieldName'];
        $targetEntity = $associationMapping['targetEntity'];

        $targetEntityAlias = $request->useStatementManipulator->addUseStatementIfNecessary($targetEntity);
        if ($request->reflectionClass->getName() === $targetEntity) {
            $targetEntityAlias = 'self';
        }
        $collectionAlias = $request->useStatementManipulator->addUseStatementIfNecessary(Collection::class);
        $collectionAliasInConstructor = $request->useStatementManipulator->addUseStatementIfNecessary(ArrayCollection::class);

        $addMethodName = $this->buildMethodName(self::TYPE_ADD, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $addMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, $block.'_add', [
                'methodName' => $addMethodName,
                'foreignMethodName' => $foreignMethodNameAdd,
                'fieldName' => $fieldName,
                'variableName' => $this->buildVariableName(self::TYPE_ADD, $fieldName),
                'targetEntity' => $targetEntity,
                'targetEntityAlias' => $targetEntityAlias,
                'request' => $request,
                'associationMapping' => $associationMapping,
                'collectionAlias' => $collectionAlias,
            ]);
        }

        $removeMethodName = $this->buildMethodName(self::TYPE_REMOVE, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $removeMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, $block.'_remove', [
                'methodName' => $removeMethodName,
                'foreignMethodName' => $foreignMethodNameRemove,
                'fieldName' => $fieldName,
                'variableName' => $this->buildVariableName(self::TYPE_REMOVE, $fieldName),
                'targetEntity' => $targetEntity,
                'targetEntityAlias' => $targetEntityAlias,
                'request' => $request,
                'associationMapping' => $associationMapping,
                'collectionAlias' => $collectionAlias,
            ]);
        }

        $getMethodName = $this->buildMethodName(self::TYPE_GET, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $getMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, 'assocation_to_many_get', [
                'methodName' => $getMethodName,
                'fieldName' => $fieldName,
                'targetEntity' => $targetEntity,
                'targetEntityAlias' => $targetEntityAlias,
                'request' => $request,
                'associationMapping' => $associationMapping,
                'collectionAlias' => $collectionAlias,
            ]);
        }

        $request->newConstructorLines[] = $this->renderBlock($request->reflectionClass, 'assocation_to_many_constructor', [
            'fieldName' => $fieldName,
            'targetEntity' => $targetEntity,
            'targetEntityAlias' => $targetEntityAlias,
            'request' => $request,
            'associationMapping' => $associationMapping,
            'collectionAlias' => $collectionAlias,
            'collectionAliasInConstructor' => $collectionAliasInConstructor,
        ]);
    }

    protected function buildMethodName(string $type, string $fieldName): string
    {
        $methodName = $type.$this->inflector->classify($fieldName);
        if (\in_array($type, [self::TYPE_ADD, self::TYPE_REMOVE])) {
            $methodName = $this->inflector->singularize($methodName);
        }

        return $methodName;
    }

    protected function buildVariableName(string $type, string $variableName): string
    {
        $variableName = $this->inflector->camelize($variableName);
        if (\in_array($type, [self::TYPE_ADD, self::TYPE_REMOVE])) {
            $variableName = $this->inflector->singularize($variableName);
        }

        return $variableName;
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     */
    protected function writeFile(\ReflectionClass $reflectionClass, string $content): void
    {
        file_put_contents((string) $reflectionClass->getFileName(), $content);
    }
}
