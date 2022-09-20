<?php


namespace TWithers\LaravelAttributes;

use Illuminate\Support\Collection;

class AttributeCollection extends Collection
{
    public function whereClass(string $className): Collection
    {
        return $this->where('class', $className);
    }

    public function whereMethod(string $className, ?string $methodName = null): Collection
    {
        $method = $className;
        if ($methodName !== null) {
            $method .= "::" . $methodName;
        }
        return $this->where('method', $method);
    }

    public function whereAttribute(string $attributeName): Collection
    {
        return $this->where('attribute', $attributeName);
    }

    public function getInstance(string $classOrMethodName, string $attributeName)
    {
        if (str_contains($classOrMethodName, "::")) {
            $this->whereMethod($classOrMethodName);
        } else {
            $this->whereClass($classOrMethodName);
        }
        $attributeItem = $this->whereAttribute($attributeName)->first();

        return $attributeItem?->instance;

    }}