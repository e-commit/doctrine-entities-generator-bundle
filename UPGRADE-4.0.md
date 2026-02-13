# UPGRADE FROM 3.x to 4.0

## Main

* Doctrine decimal type getters and setters now always use string values.
* Getter/setter generation now prioritizes PHP property types over Doctrine types.
  If a property has a PHP type, it will be used for the generated getters/setters. 
  If no PHP type is declared, the getter/setter type will be inferred from the Doctrine type.
  This behavior can be changed by customizing the template (see the FAQ).
* Getter/setter generation now follows PHP nullability: when a PHP property type is present, generated types are nullable
  only if the property type is nullable. When the PHP type is missing, generated types default to nullable.
  This behavior can be changed by customizing the template (see the FAQ).

## Template

* When generating getters/setters :
  * The `types` variable passed to the Twig template no longer exists.
     It has been replaced by the `type` variable, which returns a `Symfony\Component\TypeInfo\Type` object or null.
  * The `phpType` variable passed to the Twig template no longer exists.
     It has been replaced by the `reflectionProperty` variable, which returns a `\ReflectionProperty` object.
  * The `enumAlias`, `targetClassAlias`, `targetEntityAlias`, `collectionAlias`, and `collectionAliasInConstructor` variables
    passed to the Twig template no longer exist. Use `request.useStatementManipulator.addUseStatementIfNecessary` instead.
