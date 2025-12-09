# UPGRADE FROM 3.x to 4.0

## Main

* Doctrine decimal type getters and setters now always use string values

## Template

* When generating getters/setters for a field, the `types` variable passed to the Twig template no longer exists.
  It has been replaced by the `type` variable, which returns a `Symfony\Component\TypeInfo\Type` object or null.

