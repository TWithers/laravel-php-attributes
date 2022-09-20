# laravel-php-attributes

This package will help you create and use your own PHP 8 Attributes inside of a Laravel application without having to worry about writing your own loaders, reflection methods, and data caching.

By default, all attributes in your `app` directory are automatically loaded and cached, with an accessor provided to look up attributes as needed.

## Example

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

Normally we would need to write code to look up the attributes:

```php
$reflectionClass = new ReflectionClass(SampleClass::class);
$attributes = $reflectionClass->getAttributes(SampleMethod::class);
if (count($attributes)){
    $attribute = $attributes[0]->newInstance();
    dump($attribute->name);
    dump($attribute->label);
}

$reflectionClass = new ReflectionClass(SampleClass::class);
$reflectionMethod = $reflectionClass->getMethod('sampleMethod');
$attributes = $reflectionMethod->getAttributes(SampleMethod::class);
if (count($attributes)){
    $attribute = $attributes[0]->newInstance();
    dump($attribute->name);
    dump($attribute->label);
}

```

It isn't much code, but it isn't fast code either. With the Laravel PHP Attributes package you can use the `Attributes` facade to access those values and instances anywhere in your application.

```php
/** array{class: string, method: null, attribute: string, instance: AttributeInstance} */
$attribute = Attributes::whereClass(SampleClass::class)->first();
dump($attribute['instance']->name);
dump($attribute['instance']->label);

/** array{class: null, method: string, attribute: string, instance: AttributeInstance} */
$attribute = Attributes::whereMethod(SampleClass::class, 'sampleMethod')->first();
dump($attribute['instance']->name);
dump($attribute['instance']->label);

$attribute = Attributes::getInstance(SampleClass::class, SampleAttribute::class);
dump($attribute->name);
dump($attribute->label);
```

The `Attribute` facade gives you access to a Collection with all attributes populated and initialized. You can loop or query them how ever you want.