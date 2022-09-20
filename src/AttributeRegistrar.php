<?php

namespace TWithers\LaravelAttributes;

use Symfony\Component\Finder\Finder;

class AttributeRegistrar
{
    protected array $directories;
    protected array $attributes;

    protected array $attributeMap = [];

    public function __construct(array $directories = [], array $attributes = [])
    {
        $this->setDirectories($directories)->setAttributes($attributes);
    }

    public function setDirectories(array $directories): static
    {
        $this->directories = $directories;
        return $this;
    }

    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function register(): void
    {
        $this->findAttributes();
        $this->registerAttributes();
    }

    protected function registerAttributes(): void
    {
        $this->callableForClass(function(string $className) {
            $reflectionClass = new \ReflectionClass($className);
            $classAttributes = $reflectionClass->getAttributes();
            if (!empty($classAttributes)) {
                foreach ($classAttributes as $classAttribute) {
                    $this->checkAndInsertAttribute($className, $classAttribute);
                }
            }

            foreach ($reflectionClass->getMethods() as $method) {
                $methodAttributes = $method->getAttributes();
                if (!empty($methodAttributes)) {
                    $methodName = $className."::".$method->getName();
                    foreach($methodAttributes as $methodAttribute) {
                        $this->checkAndInsertAttribute($methodName, $methodAttribute);
                    }
                }
            }
        });
    }

    protected function findAttributes(): array
    {
        if (!empty($this->attributes)) {
            return $this->attributes;
        }

        $this->callableForClass(function(string $className) {
            $reflectionClass = new \ReflectionClass($className);
            $classAttributes = $reflectionClass->getAttributes();
            if (!empty($classAttributes) && $classAttributes[0]->getName() === 'Attribute') {
                $this->attributes[] = $className;
            }
        });

        return $this->attributes;
    }

    protected function callableForClass(callable $function): void
    {
        foreach ($this->directories as $namespace => $path) {
            foreach ((new Finder)->in($path)->files()->name('*.php') as $file) {
                $className = $namespace . "\\";
                if (strlen($file->getRelativePath())) {
                    $className .= str_replace("/", "\\", $file->getRelativePath()) . "\\";
                }
                $className .= $file->getFilenameWithoutExtension();
                if (class_exists($className)) {
                    call_user_func($function, $className);
                }
            }
        }
    }

    protected function checkAndInsertAttribute($key, \ReflectionAttribute $attribute): void
    {
        if (( empty($this->attributes) || in_array($attribute->getName(), $this->attributes) ) && class_exists($attribute->getName())) {
            $attributeInstance = $attribute->newInstance();
            if (!isset($this->attributeMap[$key])) {
                $this->attributeMap[$key] = [];
            }
            if (!isset($this->attributeMap[$key][$attribute->getName()])) {
                $this->attributeMap[$key][$attribute->getName()] = [];
            }
            $this->attributeMap[$key][$attribute->getName()][] = serialize($attributeInstance);
        }
    }

    public function getAttributeMap(): array
    {
        return $this->attributeMap;
    }

}