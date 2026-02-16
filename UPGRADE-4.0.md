# UPGRADE FROM 3.x to 4.0

## Main (if default template is used)

* Doctrine decimal type getters and setters now always use string values.
* Getter/setter generation now prioritizes PHP property types over Doctrine types.
  If a property has a PHP type, it will be used for the generated getters/setters. 
  If no PHP type is declared, the getter/setter type will be inferred from the Doctrine type.
  This behavior can be changed by customizing the template (see the FAQ).
* Getter/setter generation now follows PHP nullability: when a PHP property type is present, generated types are nullable
  only if the property type is nullable. When the PHP type is missing, generated types default to nullable.
  This behavior can be changed by customizing the template (see the FAQ).
* Now, if a property has a PHPDoc `@var` annotation, it will also be included in the generated getter/setter (except for `addXXX` and `removeXXX` methods on to-many relationships).
  This behavior can be changed by customizing the template (see the FAQ).
* Now, the PHPDoc `@return Collection<int, xxx>` is no longer automatically generated on the getter of a to-many relationship, unless the corresponding 
  PHPDoc (`@var Collection<int, xxx>`) is present on the property (see previous point). To keep the PHPDoc on the getter, add the PHPDoc to the property.
  This behavior can be changed by customizing the template (see the FAQ).

## Template customization (if another template is used)

* When generating getters/setters :
  * The `types` variable passed to the Twig template no longer exists.
     It has been replaced by the `type` variable, which returns a `Symfony\Component\TypeInfo\Type` object or null.
  * The `phpType` variable passed to the Twig template no longer exists.
     It has been replaced by the `reflectionProperty` variable, which returns a `\ReflectionProperty` object.
  * The `enumAlias`, `targetClassAlias`, `targetEntityAlias`, `collectionAlias`, and `collectionAliasInConstructor` variables
    passed to the Twig template no longer exist. Use `request.useStatementManipulator.addUseStatementIfNecessary` instead.

## `Ecommit\DoctrineEntitiesGeneratorBundle\EntityGenerator\EntityGenerator` class

The signature of the following method have been modified :

Before :

```php
protected function propertyIsDefinedInClassFile(GenerateEntityRequest $request, string $property): bool
```

After :

```php
protected function propertyIsDefinedInClassFile(\ReflectionClass $reflectionClass, string $property): bool
```
