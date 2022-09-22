<?php

namespace TWithers\LaravelAttributes\Attribute;

use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;

class AttributeAccessor
{

    protected int $type;
    protected string $className;
    protected ?string $identifier;

    public function __construct(int $type, string $className, ?string $identifier = null)
    {
        $this->type = $type;
        $this->className = $className;
        $this->identifier = $identifier;
    }

    /**
     * @param string $className
     * @return AttributeInstance[]|null
     */
    public static function forClass(string $className): ?array
    {
        $accessor = new static(AttributeTarget::TYPE_CLASS, $className, null);
        return $accessor->reflectForAttributes();
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return AttributeInstance[]|null
     */
    public static function forClassMethod(string $className, string $methodName): ?array
    {
        $accessor = new static(AttributeTarget::TYPE_METHOD, $className, $methodName);
        return $accessor->reflectForAttributes();
    }

    /**
     * @param string $className
     * @param string $propertyName
     * @return AttributeInstance[]|null
     */
    public static function forClassProperty(string $className, string $propertyName): ?array
    {
        $accessor = new static(AttributeTarget::TYPE_PROPERTY, $className, $propertyName);
        return $accessor->reflectForAttributes();
    }

    /**
     * @return AttributeInstance[]|null
     */
    public function reflectForAttributes(): ?array
    {
        try{
            $reflection = match ($this->type) {
                AttributeTarget::TYPE_CLASS => new \ReflectionClass($this->className),
                AttributeTarget::TYPE_METHOD => (new \ReflectionClass($this->className))->getMethod($this->identifier),
                AttributeTarget::TYPE_PROPERTY => (new \ReflectionClass($this->className))->getProperty($this->identifier),
            };
        } catch (\ReflectionException $e) {
            return null;
        }

        $attributeInstances = [];

        foreach($reflection->getAttributes() as $attribute) {
            if (class_exists($attribute->getName())) {
                $attributeInstances[] = new AttributeInstance($attribute->getName(), $attribute->newInstance());
            }
        }

        return $attributeInstances;
    }

}