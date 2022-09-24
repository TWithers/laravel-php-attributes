<?php

namespace TWithers\LaravelAttributes\Facades;

use Illuminate\Support\Facades\Facade;
use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;

/**
 * @method static AttributeTarget add(int $type, string $className, ?string $identifier, string $attributeName, object $attributeInstance)
 * @method static AttributeTarget addInstance(int $type, string $className, ?string $identifier, AttributeInstance $attributeInstance)
 * @method static AttributeTarget[] all()
 * @method static int count()
 * @method static AttributeTarget|null find(int $type, string $className, ?string $identifier = null)
 * @method static AttributeTarget|null findByClass(string $className)
 * @method static AttributeTarget|null findByClassMethod(string $className, string $methodName)
 * @method static AttributeTarget|null findByClassProperty(string $className, string $propertyName)
 * @method static AttributeTarget[] findTargetsWithAttribute(string $attributeName, ?int $type = null)
 *
 * @see AttributeCollection
 */
class Attributes extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'attributes';
    }
}
