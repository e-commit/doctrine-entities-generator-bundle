UPGRADE FROM 1.x to 2.0

* Remove Doctrine annotations support in favor of native attributes
* Rename `Ecommit\DoctrineEntitiesGeneratorBundle\Annotations\IgnoreGenerateEntity` to `Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\IgnoreGenerateEntity`
* Rename `Ecommit\DoctrineEntitiesGeneratorBundle\Annotations\GenerateEntityTemplate` to `Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\GenerateEntityTemplate`
* The signature of method `Ecommit\DoctrineEntitiesGeneratorBundle\Annotations\GenerateEntityTemplate::__construct` has been updated to `Ecommit\DoctrineEntitiesGeneratorBundle\Annotations\GenerateEntityTemplate::__construct(string $template)`
