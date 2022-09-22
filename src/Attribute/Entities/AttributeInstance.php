<?php

namespace TWithers\LaravelAttributes\Attribute\Entities;

class AttributeInstance
{
    public string $name;
    public object $instance;

    public function __construct(string $name, object $instance)
    {
        $this->name = $name;
        $this->instance = $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'instance' => $this->instance,
        ];
    }

}
