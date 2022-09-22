# laravel-php-attributes

This package will help you create and use your own PHP 8 Attributes inside of a Laravel application without having to worry about writing your own loaders, reflection methods, and data caching.

By default, all attributes in your `app` directory are automatically loaded and cached, with an accessor provided to look up attributes as needed.
The package can be configured to limit directories as well as attributes to speed up loading and minimize caching.

- [Installation](#installation)
- [PHP 8 Attributes Overview](#attributes-in-php-8)
- [Usage](#usage)
  - [AttributeCollection Facade](#accessing-the-attributecollection-api)
  - [AttributeTarget API](#attributetarget-api)
  - [Standalone Usage](#standalone-usage)
- [Commands](#clearing-attribute-cache)
- [Testing](#testing)
- [Contributing](#contributing)
- [License Information](#license)


## Installation

Require this package with composer using the following command:

```bash
composer require twithers/laravel-php-attributes
```
You can publish the config file with:
```bash
php artisan vendor:publish --provider="TWithers\LaravelAttributes\AttributesServiceProvider" --tag="config"
```

These are the contents of the published config file:
```php
return [
    
    /**
     * Caching will make use of Laravel's built-in file caching. Using caching will be a massive performance benefit
     * as no directories and files need to be scanned for attributes and attribute usages
     */
    'use_cache' => true,

    /**
     * By default this will scan your listed directories below for all attributes and then search for them.
     *
     * If you want to avoid the initial search, you can list your attribute classes below:
     *     'App\Attributes\Foo',
     *     App\Attributes\Bar::class,
     *
     */
    'attributes' => [

    ],

    /**
     * By default this will scan all files inside your app folder for attributes.
     *
     * If you want to limit the folders, you can adjust the namespace and the files:
     * 'App\Http\Controllers' => app_path('Http/Controllers')
     */
    'directories' => [
        'App' => app_path(),
    ],

];
```

You can disable caching, but it is not recommended as each request would scan for and establish the attribute collection.

You can limit attributes to a specific list by declaring them. The empty array indicates that ALL attributes found in the listed
directories will be scanned for.

You can narrow down the directory search, or list multiple separate directories to find attributes and their usage. Ensure your
array is structured with `$namespace => $directoryPath` to ensure files are correctly found and accessed.

## Attributes in PHP 8

First attributes are defined and namespaced. An `Attribute` attribute is then applied to it. 
After defining an attribute, we can then add it to anything we want: classes, methods, functions, properties, etc.
Below is a example of how you would define and use an attribute.
```php
#[Attribute]
class SampleAttribute
{
    public string $name;
    public string $label;
    
    public function __construct(
        string $name,
        string $label
    ){
        $this->name = name;
        $this->label = label;
    }
}

#[SampleAttribute(name: 'SampleClass', label: 'My Sample Class Label')]
class SampleClass
{
    #[SampleMethod(name: 'sampleMethod', label: 'My Sample Method Label')]
    public function sampleMethod(): bool
    { 
        return true;
    }
}
```

We have a defined attribute `SampleAttribue` that requires a `name` and a `label`. 
We have a `SampleClass` that uses that attribute, as well as the method `sampleMethod()` that also uses that attribute.

### Looking up Attributes in PHP 8+

Attributes by themselves do nothing. PHP currently doesn't give you a super easy way to
capture attributes and values. You are left using PHP Reflection, which may not be something you have ever used.
Reflection is just that, reflecting on the code. You pass in some information, such as class names, methods names, etc, and 
the Reflection API will give you all the info you need to know about it... including attributes.

Below is how you would currently use the Reflection API to look up attributes.

```php
$reflectionClass = new ReflectionClass(SampleClass::class);
$attributes = $reflectionClass->getAttributes(SampleMethod::class);
if (count($attributes)){
    $attribute = $attributes[0]->newInstance();
    dump($attribute->name); // "SampleClass"
    dump($attribute->label); // "My Sample Class Label"
}

$reflectionClass = new ReflectionClass(SampleClass::class);
$reflectionMethod = $reflectionClass->getMethod('sampleMethod');
$attributes = $reflectionMethod->getAttributes(SampleMethod::class);
if (count($attributes)){
    $attribute = $attributes[0]->newInstance();
    dump($attribute->name); // "Sample Method"
    dump($attribute->label); // "My Sample Method"
}
```
This isn't much code to write and pretty easy to follow. If you are using attributes throughout your application, however, you
will soon find yourself repeating this code in many places.

**Enter Laravel PHP Attributes**

## Usage

### Accessing the AttributeCollection API

This package provides the `Attributes` facade to help you access the stored attribute collection data after it is processed.
You can also type-hint the `AttributeCollection` class throughout your app and Laravel will autoload the initialized attribute
collection.

The `Attributes` facade and `AttributeCollection` provides the following useful methods:

```php
Attributes::findByClass(string $className): ?AttributeTarget
Attributes::findByClassMethod(string $className, string $methodName): ?AttributeTarget
Attributes::findByClassProperty(string $className, string $propertyName): ?AttributeTarget
Attributes::findTargetsWithAttribute(string $attributeName): AttributeTarget[]
```
An `AttributeTarget` is the class, method, or property that the attribute is attached to.
The `AttributeTarget` has the following structure:
- **int $type**: Indicates if the target is a class, method, or property
- **string $className**: The full namespaced name of the class that was targeted or contains the attribute
- **?string $identifier**: The name of the method or property
- **allAttributes(): AttributeInstance[]**: The method to retrieve a list of all attached attributes (`$name` and instantiated `$instance`)

Here is how the code from above could be rewritten:
```php
$attributes = Attributes::findByClass(SampleClass::class)->allAttributes();
dump($attributes[0]->instance->name); // "SampleClass"
dump($attributes[0]->instance->label); // "My Sample Class Label"

$attributes = Attributes::findByClassMethod(SampleClass::class, 'sampleMethod')->allAttributes();
dump($attributes[0]->instance->name); // "Sample Method"
dump($attributes[0]->instance->label); // "My Sample Method"
```

### AttributeTarget API
The `AttributeTarget` class exposes a few helpful methods as well for finding specific attributes:
```php
AttributeTarget::allAttributes(): AttributeInstance[]
AttributeTarget::hasAttribute(string $attributeName): bool
AttributeTarget::findByName(string $attributeName): AttributeInstance[]
```

### Standalone Usage

If you do not want to utilize the Laravel service provider's automatic loading and caching of attributes, you can remove it from
the list of loaded service providers. You will be able to use the `AttributeAccessor` class to look up attributes if you know the
pertinent info. Here are the methods that are available:

```php
AttributeAccessor::forClass(string $className): ?AttributeInstance[]
AttributeAccessor::forClassMethod(string $className, string $methodName): ?AttributeInstance[]
AttributeAccessor::forClassProperty(string $className, string $propertyName): ?AttributeInstance[]
```
If the class, method, or property are not found, it will return `null`, otherwise, each of those static functions will look up
all attached attributes and return an array of `AttributeInstances` which have both the `$name` and `$instance` of the attribute
for your use.

## Clearing Attribute Cache

By default, attributes will be found and processed, with the results cached on the first request. Subsequent requests will utilize
the cached data to avoid costly processing time. This means that changes to attributes or methods that are using attributes will require
the cache to be cleared. By default (unless your environment is set to store files in a different location), the cached file is stored in `bootstrap\cache\attributes.php` and can be manually deleted. You can also 
use the following command:
```bash
php artisan attributes:clear
```
It would be pertinent to use this command in your deployment process to ensure any added attributes will be properly cached.


## Testing
```bash
./vendor/bin/phpunit
```

## Contributing
All contributions are welcome.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


