# UPGRADE FROM 2.x to 3.0

## Doctrine ORM

The bundle is no longer compatible with Doctrine ORM v2. Version 3 (≥ 3.2) is required.


## `Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\EntityGenerator` class

The signatures of the following methods have been modified :

Before :

```php
protected function addField(GenerateEntityRequest $request, array $fieldMapping): void
protected function addEmbedded(GenerateEntityRequest $request, string $fieldName, array $embeddedMapping): void
protected function addAssociation(GenerateEntityRequest $request, array $associationMapping): void
protected function addAssociationToOne(GenerateEntityRequest $request, array $associationMapping, string $block, ?string $foreignMethodNameSet): void
protected function addAssociationToMany(GenerateEntityRequest $request, array $associationMapping, string $block, ?string $foreignMethodNameAdd, ?string $foreignMethodNameRemove): void
```

After :

```php
protected function addField(GenerateEntityRequest $request, FieldMapping $fieldMapping): void
protected function addEmbedded(GenerateEntityRequest $request, string $fieldName, EmbeddedClassMapping $embeddedMapping): void
protected function addAssociation(GenerateEntityRequest $request, AssociationMapping $associationMapping): void
protected function addAssociationToOne(GenerateEntityRequest $request, AssociationMapping $associationMapping, string $block, ?string $foreignMethodNameSet): void
protected function addAssociationToMany(GenerateEntityRequest $request, AssociationMapping $associationMapping, string $block, ?string $foreignMethodNameAdd, ?string $foreignMethodNameRemove): void
```

With the following PHP classes:

* `Doctrine\ORM\Mapping\AssociationMapping`
* `Doctrine\ORM\Mapping\EmbeddedClassMapping`
* `Doctrine\ORM\Mapping\FieldMapping`


## Template

The following variables available in the template are no longer arrays :

* `fieldMapping` : This variable is now a object of `Doctrine\ORM\Mapping\AssociationMapping`.
* `embeddedMapping` : This variable is now a object of `Doctrine\ORM\Mapping\EmbeddedClassMapping`.
* `associationMapping` : This variable is now a object of `Doctrine\ORM\Mapping\FieldMapping`.

## PHP type hint

Classes now use PHP type hints.
