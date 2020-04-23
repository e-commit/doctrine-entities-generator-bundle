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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ManagerRegistry;
use Ecommit\DoctrineEntitiesGeneratorBundle\Annotations\GenerateEntityTemplate;
use Ecommit\DoctrineEntitiesGeneratorBundle\Entity\EntityInitializerInterface;
use Ecommit\DoctrineEntitiesGeneratorBundle\EntitySearcher\EntitySearcherInterface;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\ClassNotManagedException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\EntityInitializerInterfaceNotUsedException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Exception\TagNotFoundException;
use Ecommit\DoctrineEntitiesGeneratorBundle\Model\GenerateEntityRequest;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Twig\Environment;

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

    protected $template;

    public function __construct(EntitySearcherInterface $searcher, ManagerRegistry $registry, Environment $twig, string $template)
    {
        $this->searcher = $searcher;
        $this->registry = $registry;
        $this->twig = $twig;
        $this->template = $template;
    }

    public function generate(string $className): void
    {
        $reflectionClass = new \ReflectionClass($className);
        $entityManager = $this->registry->getManagerForClass($className);
        if (!$entityManager) {
            throw new ClassNotManagedException(sprintf('Class "%s" not managed', $className));
        }
        $metadata = $entityManager->getClassMetadata($reflectionClass->getName());

        if (!$this->searcher->classCanBeGenerated($metadata)) {
            throw new ClassNotManagedException(sprintf('Class "%s" cannot be generated (Is IgnoreGenerateEntity annotation used ?)', $className));
        }

        $fileParts = $this->getFileParts($reflectionClass);
        $doctrineExtractor = new DoctrineExtractor($entityManager);
        $request = new GenerateEntityRequest(
            $reflectionClass,
            $fileParts,
            $metadata,
            $doctrineExtractor
        );

        foreach ($doctrineExtractor->getProperties($className) as $property) {
            if (!$this->propertyIsDefinedInClassFile($request, $property)) {
                continue;
            }

            if ($metadata->hasField($property)) {
                $this->addField($request, $metadata->fieldMappings[$property]);
            } elseif ($metadata->hasAssociation($property)) {
                $this->addAssociation($request, $metadata->associationMappings[$property]);
            }
        }

        if (is_subclass_of($className, EntityInitializerInterface::class)) {
            $request->addInitializeEntity = true;
        }

        if (\count($request->newConstructorLines) > 0 || $request->addInitializeEntity) {
            if ($this->methodIsDefinedOutsideBlock($request, '__construct')) {
                throw new EntityInitializerInterfaceNotUsedException(sprintf('Class "%s": __construct method is used. Remove it and implement "%s" interface', $className, EntityInitializerInterface::class));
            }

            array_unshift($request->newBlockContents, $this->renderBlock($reflectionClass, 'constructor', [
                'request' => $request,
            ]));
        }

        $newBlockContent = $this->renderBlock($reflectionClass, 'block_content', [
            'request' => $request,
        ]);

        $content = file_get_contents($reflectionClass->getFileName());
        $content = preg_replace($this->getPattern($reflectionClass), sprintf('$1$2%s$4$5', $newBlockContent), $content);
        $this->writeFile($reflectionClass, $content);
    }

    protected function getFileParts(\ReflectionClass $reflectionClass): array
    {
        $content = file_get_contents($reflectionClass->getFileName());
        $pattern = $this->getPattern($reflectionClass);
        if (!preg_match($pattern, $content, $fileParts)) {
            throw new TagNotFoundException(sprintf('Class "%s": Start tag or end tag is not found', $reflectionClass->getName()));
        }

        return $fileParts;
    }

    protected function getPattern(\ReflectionClass $reflectionClass): string
    {
        $startTag = $this->renderBlock($reflectionClass, 'start_tag');
        $endTag = $this->renderBlock($reflectionClass, 'end_tag');

        return sprintf(
            '/^(?P<beforeBlock>.*)(?P<startTag>%s)(?P<block>.*)(?P<endTag>%s)(?P<afterBlock>.*)/is',
            preg_quote($startTag, '/'),
            preg_quote($endTag, '/')
        );
    }

    protected function renderBlock(\ReflectionClass $reflectionClass, string $blockName, array $parameters = []): string
    {
        //Load Doctrine annotations
        $this->registry->getManagerForClass($reflectionClass->getName());

        $templateName = $this->template;
        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation($reflectionClass, GenerateEntityTemplate::class);
        if ($annotation && null !== $annotation->value) {
            $templateName = $annotation->value;
        }

        $template = $this->twig->load($templateName);

        ob_start();

        $template->displayBlock($blockName, $parameters);

        $content = ob_get_clean();

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

        $startTagLine = \count(explode(PHP_EOL, $request->fileParts['beforeBlock']));
        if ($reflectionMethod->getStartLine() < $startTagLine) {
            return true;
        }

        $endBlockLine = $startTagLine + \count(explode(PHP_EOL, $request->fileParts['block']));
        if ($reflectionMethod->getStartLine() > $endBlockLine) {
            return true;
        }

        return false;
    }

    protected function addField(GenerateEntityRequest $request, array $fieldMapping): void
    {
        $fieldName = $fieldMapping['fieldName'];
        $types = $request->doctrineExtractor->getTypes($request->reflectionClass->getName(), $fieldName);

        if (null === $request->doctrineExtractor->isWritable($request->reflectionClass->getName(), $fieldName)) {
            $setMethodName = $this->buildMethodName(self::TYPE_SET, $fieldName);
            if (!$this->methodIsDefinedOutsideBlock($request, $setMethodName)) {
                $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, 'field_set', [
                    'methodName' => $setMethodName,
                    'fieldName' => $fieldName,
                    'variableName' => $this->buildVariableName(self::TYPE_SET, $fieldName),
                    'types' => $types,
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
                'request' => $request,
                'fieldMapping' => $fieldMapping,
            ]);
        }
    }

    protected function addAssociation(GenerateEntityRequest $request, array $associationMapping): void
    {
        if ($associationMapping['type'] & ClassMetadataInfo::TO_ONE && $associationMapping['mappedBy']) {
            $this->addAssociationToOne(
                $request,
                $associationMapping,
                'assocation_one_to_one_reverse',
                $this->buildMethodName(self::TYPE_SET, $associationMapping['mappedBy'])
            );
        } elseif ($associationMapping['type'] & ClassMetadataInfo::TO_ONE && $associationMapping['inversedBy']) {
            $this->addAssociationToOne(
                $request,
                $associationMapping,
                'assocation_one_to_one_owning',
                null
            );
        } elseif ($associationMapping['type'] & ClassMetadataInfo::TO_ONE) {
            $this->addAssociationToOne(
                $request,
                $associationMapping,
                'assocation_one_to_one_unidirectional',
                null
            );
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

    protected function addAssociationToOne(GenerateEntityRequest $request, array $associationMapping, string $block, ?string $foreignMethodNameSet): void
    {
        $fieldName = $associationMapping['fieldName'];
        $targetEntity = $associationMapping['targetEntity'];

        $setMethodName = $this->buildMethodName(self::TYPE_SET, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $setMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, $block.'_set', [
                'methodName' => $setMethodName,
                'foreignMethodName' => $foreignMethodNameSet,
                'fieldName' => $fieldName,
                'variableName' => $this->buildVariableName(self::TYPE_SET, $fieldName),
                'targetEntity' => $targetEntity,
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
                'request' => $request,
                'associationMapping' => $associationMapping,
            ]);
        }
    }

    protected function addAssociationToMany(GenerateEntityRequest $request, array $associationMapping, string $block, ?string $foreignMethodNameAdd, ?string $foreignMethodNameRemove): void
    {
        $fieldName = $associationMapping['fieldName'];
        $targetEntity = $associationMapping['targetEntity'];

        $addMethodName = $this->buildMethodName(self::TYPE_ADD, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $addMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, $block.'_add', [
                'methodName' => $addMethodName,
                'foreignMethodName' => $foreignMethodNameAdd,
                'fieldName' => $fieldName,
                'variableName' => $this->buildVariableName(self::TYPE_ADD, $fieldName),
                'targetEntity' => $targetEntity,
                'request' => $request,
                'associationMapping' => $associationMapping,
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
                'request' => $request,
                'associationMapping' => $associationMapping,
            ]);
        }

        $getMethodName = $this->buildMethodName(self::TYPE_GET, $fieldName);
        if (!$this->methodIsDefinedOutsideBlock($request, $getMethodName)) {
            $request->newBlockContents[] = $this->renderBlock($request->reflectionClass, 'assocation_to_many_get', [
                'methodName' => $getMethodName,
                'fieldName' => $fieldName,
                'targetEntity' => $targetEntity,
                'request' => $request,
                'associationMapping' => $associationMapping,
            ]);
        }

        $request->newConstructorLines[] = $this->renderBlock($request->reflectionClass, 'assocation_to_many_constructor', [
            'fieldName' => $fieldName,
            'targetEntity' => $targetEntity,
            'request' => $request,
            'associationMapping' => $associationMapping,
        ]);
    }

    protected function buildMethodName(string $type, string $fieldName): string
    {
        $methodName = $type.Inflector::classify($fieldName);
        if (\in_array($type, [self::TYPE_ADD, self::TYPE_REMOVE])) {
            $methodName = Inflector::singularize($methodName);
        }

        return $methodName;
    }

    protected function buildVariableName(string $type, string $variableName): string
    {
        $variableName = Inflector::camelize($variableName);
        if (\in_array($type, [self::TYPE_ADD, self::TYPE_REMOVE])) {
            $variableName = Inflector::singularize($variableName);
        }

        return $variableName;
    }

    protected function writeFile(\ReflectionClass $reflectionClass, string $content): void
    {
        file_put_contents($reflectionClass->getFileName(), $content);
    }
}
