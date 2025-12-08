# EcommitDoctrineEntitiesGeneratorBundle

The EcommitDoctrineEntitiesGeneratorBundle bundle (for Symfony) allows the user to (re)generate getters-setters 
methods for Doctrine ORM entities.


![Tests](https://github.com/e-commit/doctrine-entities-generator-bundle/workflows/Tests/badge.svg)


## Installation ##

Install the bundle with Composer : In your project directory, execute the following command :

```bash
$ composer require ecommit/doctrine-entities-generator-bundle
```

Enable the bundle in the `config/bundles.php` file for your project :

```php
return [
    //...
    Ecommit\DoctrineEntitiesGeneratorBundle\EcommitDoctrineEntitiesGeneratorBundle::class => ['dev' => true],
    //...
];
```

## Usage ##

Add the start tag to your entity :

```php
    /*
     * Getters / Setters (auto-generated)
     */
```

**WARNING** : The content between this start tag and the end of the PHP class will be deleted
when the bundle generates the getters-setters methods. The getters-setters methods will be generated
between these two tags.


For example:

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'category_id')]
    protected $categoryId;

    #[ORM\Column(type: 'string', length: 255)]
    protected $name;

    /*
     * Getters / Setters (auto-generated)
     */

    //Content after this block will be deleted when
    //the bundle generates the getters-setters methods.
    //Getters-setters methods will be generated here.
}
```

You can change the start tag and the end tag (the end of the PHP class by default) : See the "FAQ" section.


In your project directory, execute the following command :

```bash
$ php bin/console ecommit:doctrine:generate-entities {Classename}
```

For example:

```bash
$ php bin/console ecommit:doctrine:generate-entities App/Entity/MyEntity
```

Each slash is replaced by an anti-slash.

You can use the `*` joker (which generates multiple entities). For example:

```bash
$ php bin/console ecommit:doctrine:generate-entities App/Entity/*
```

The bundle generates getters-setters methods for an entity only if :
* The PHP class is a Doctrine ORM entity; and
* The entity is not an interface; and
* The entity is not a trait; and
* The entity doesn't use the `Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\IgnoreGenerateEntity` attribute.

The bundle generates getters-setters methods for an entity property only if :
* The property is defined directly in the entity (and is not defined in an inherited class or a trait); and
* The property is not public; and
* The methods (getters-setters) do not exist (except if the method is defined between the start and end tags).

## FAQ ##

### How can I change the generated code ? ###

When the code is generated, the `@EcommitDoctrineEntitiesGenerator/Theme/base.php.twig` Twig template is used.

You can create a custom template (that extends the base template). 


**Solution 1 - Override the bundle**

See https://symfony.com/doc/current/bundles/override.html

**Solution 2 - Configure the template**

In your project configuration, you can configure the theme used by the bundle. For example, you can create 
the `config/packages/dev/ecommit_doctrine_entities_generator.yaml` file:

```yaml
ecommit_doctrine_entities_generator:
    template: "your_template.php.twig"
```

**Solution 3 - Create a custom template in entity**

You can override the theme to be used by the bundle only for an entity. To do this, use
the `Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\GenerateEntityTemplate` attribute:

```php
use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\GenerateEntityTemplate;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[GenerateEntityTemplate("your_template.php.twig")]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'category_id')]
    protected $categoryId;
    //...
}
```

### How can I change the start-end tags ? ###

You can change the template (see previous question).

The start tag is defined in the `start_tag` Twig block.

The end tag is defined in the `end_tag` Twig block.

For example, you can create this theme:

```twig
{% extends '@EcommitDoctrineEntitiesGenerator/Theme/base.php.twig' %}

{% block end_tag %}


    /*
     * End Getters / Setters (auto-generated)
     */
{% endblock %}
```

and use as follows: 

```php
use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\GenerateEntityTemplate;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[GenerateEntityTemplate('your_template.php.twig')]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'category_id')]
    protected $categoryId;
    //...

    /*
     * Getters / Setters (auto-generated)
     */


    /*
     * End Getters / Setters (auto-generated)
     */
}
```

### How can I create a constructor in my entity ? ###

If your entity has a `TOMANY` association, the bundle will create a constructor in your entity.
For this reason, manually defining a constructor in your entity is not allowed.

Instead, you can use the `Ecommit\DoctrineEntitiesGeneratorBundle\Entity\EntityInitializerInterface` interface
and its `initializeEntity` method.

```php
use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Entity\EntityInitializerInterface;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
class Category implements EntityInitializerInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'category_id')]
    protected $categoryId;

    #[ORM\OneToMany(targetEntity: 'Ecommit\DoctrineEntitiesGeneratorBundle\Tests\App\Entity\Book', mappedBy: 'category')]
    protected $books;

    #[ORM\Column(type: 'datetime')]
    protected $createdAt;

    public function initializeEntity(): void
    {
        $this->createdAt = new \DateTime('now');
    }

    //...
}
```

The `initializeEntity` method will be automatically called in the constructor generated in this way.


### An EntityInitializerInterfaceNotUsedException exception is thrown ###

An `Ecommit\DoctrineEntitiesGeneratorBundle\Exception\EntityInitializerInterfaceNotUsedException`
exception is thrown if you define manually a constructor in your entity when a `TOMANY` association is used.

See the previous question.


### A TagNotFoundException exception is thrown ###

The start and/or end tag was not found in your entity.


### How can I ignore the generation of getters-setters methods for an entity ? ###

Not all entities are processed (see the "Usage" section to find out which classes can be generated).

You can ignore the generation of getters-setters methods for an entity by using the
`Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\IgnoreGenerateEntity` attribute :

```php
use Doctrine\ORM\Mapping as ORM;
use Ecommit\DoctrineEntitiesGeneratorBundle\Attribute\IgnoreGenerateEntity;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[IgnoreGenerateEntity]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'category_id')]
    protected $categoryId;
    //...
}
```

### How can I ignore the generation of getters-setters methods for a property ? ###

Not all properties are processed (see the "Usage" section to find out which properties can be generated).

### Why was no method generated ? ###

See the last two questions.



## Limitations ##

The bundle only works under the following conditions :

* The Doctrine attributes are used (Doctrine annotations are not compatible).
* Only one entity (PHP class) per PHP file
* The getters and setters of an embeddable are generated only if it's embedded at least once in an entity
* Inside each entity (PHP class) :
    * Only one property per line
    * Only one method per line (but a method can be defined through over lines)
* EOL (End Of Line) = LF


## License ##

This bundle is available under the MIT license. See the complete license in the *LICENSE* file.
