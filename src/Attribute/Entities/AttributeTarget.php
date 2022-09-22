<?php

namespace TWithers\LaravelAttributes\Attribute\Entities;

class AttributeTarget
{
    const TYPE_CLASS = 1;
    const TYPE_METHOD = 2;
    const TYPE_PROPERTY = 3;

    public string $className;
    public ?string $identifier;
    public int $type;

    /**
     * @var AttributeInstance[]
     */
    protected array $attributeMap = [];

    public function __construct(int $type, string $className, ?string $identifier)
    {
        $this->type = $type;
        $this->className = $className;
        $this->identifier = $identifier;
    }

    /**
     * @return AttributeInstance[]
     */
    public function allAttributes(): array
    {
        return $this->attributeMap;
    }

    /**
     * @param string|AttributeInstance $attribute
     * @param object|null $instance
     * @return void
     */
    public function addAttribute(string|AttributeInstance $attribute, ?object $instance = null): void
    {
        if (is_string($attribute) && $instance !== null) {
            $this->attributeMap[] = new AttributeInstance($attribute, $instance);
        } else if ($instance === null) {
            $this->attributeMap[] = $attribute;
        }
    }


    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return (bool)count($this->findByName($name));
    }

    /**
     * @param string $name
     * @return array
     */
    public function findByName(string $name): array
    {
        return array_filter($this->attributeMap,
            fn(AttributeInstance $instance) => $instance->name === $name
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'className' => $this->className,
            'identifier' => $this->identifier,
            'attributes' => array_map(fn (AttributeInstance $instance) => $instance->toArray(), $this->attributeMap),
        ];
    }
}
