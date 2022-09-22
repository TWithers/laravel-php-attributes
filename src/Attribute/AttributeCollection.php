<?php


namespace TWithers\LaravelAttributes\Attribute;

use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;

class AttributeCollection
{

    /**
     * @var AttributeTarget[]
     */
    protected array $data;

    /**
     * @param AttributeTarget[] $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return AttributeTarget[]
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @param int $type
     * @param string $className
     * @param string|null $identifier
     * @param string $attributeName
     * @param object $attributeInstance
     * @return AttributeTarget
     */
    public function add(int $type, string $className, ?string $identifier, string $attributeName, object $attributeInstance): AttributeTarget
    {
        $target = $this->find($type, $className, $identifier);

        if ($target === null) {
            $target = new AttributeTarget($type, $className, $identifier);
            $this->data[] = $target;
        }

        $target->addAttribute($attributeName, $attributeInstance);

        return $target;
    }

    /**
     * @param int $type
     * @param string $className
     * @param string|null $identifier
     * @param AttributeInstance $attributeInstance
     * @return AttributeTarget
     */
    public function addInstance(int $type, string $className, ?string $identifier, AttributeInstance $attributeInstance): AttributeTarget
    {
        $target = $this->find($type, $className, $identifier);

        if ($target === null) {
            $target = new AttributeTarget($type, $className, $identifier);
            $this->data[] = $target;
        }

        $target->addAttribute($attributeInstance);

        return $target;
    }

    /**
     * @param int $type
     * @param string $className
     * @param string|null $identifier
     * @return AttributeTarget|null
     */
    public function find(int $type, string $className, ?string $identifier = null): ?AttributeTarget
    {
        foreach($this->data as $target) {
            if ($target->className === $className
                && ($type === AttributeTarget::TYPE_CLASS || ($target->type === $type && $target->identifier === $identifier))
            ) {
                return $target;
            }
        }
        return null;
    }

    /**
     * @param string $className
     * @return AttributeTarget|null
     */
    public function findByClass(string $className): ?AttributeTarget
    {
        return $this->find(AttributeTarget::TYPE_CLASS, $className);
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return AttributeTarget|null
     */
    public function findByClassMethod(string $className, string $methodName): ?AttributeTarget
    {
        return $this->find(AttributeTarget::TYPE_METHOD, $className, $methodName);
    }

    /**
     * @param string $className
     * @param string $propertyName
     * @return AttributeTarget|null
     */
    public function findByClassProperty(string $className, string $propertyName): ?AttributeTarget
    {
        return $this->find(AttributeTarget::TYPE_METHOD, $className, $propertyName);
    }


    /**
     * @param string $attributeName
     * @param int|null $type
     * @return AttributeTarget[]
     */
    public function findTargetsWithAttribute(string $attributeName, ?int $type = null): array
    {
        return array_filter(
            $this->data,
            fn(AttributeTarget $target) => $target->hasAttribute($attributeName)
                && ($type === null || $target->type === $type)
        );
    }

    public function __serialize(): array
    {
        return [
            'data' => $this->data
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->data = $data['data'];
    }
}