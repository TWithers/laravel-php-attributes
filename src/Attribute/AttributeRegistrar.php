<?php

namespace TWithers\LaravelAttributes\Attribute;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Mime\Part\Multipart\RelatedPart;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;

class AttributeRegistrar
{
    protected array $directories;
    protected array $attributes;
    protected AttributeCollection $collection;

    /**
     * An array of directories and attributes to search for
     *
     * @param array{'namespace': string, 'path': string} $directories
     * @param class-string[] $attributes
     */
    public function __construct(array $directories = [], array $attributes = [])
    {
        $this->collection = new AttributeCollection();

        $this->setDirectories($directories)->setAttributes($attributes);
    }

    /**
     * An array of directories to search for attributes
     *
     * @param array{'namespace': string, 'path': string} $directories
     * @return $this
     */
    public function setDirectories(array $directories): static
    {
        $this->directories = $directories;
        return $this;
    }

    /**
     * An array of attributes to limit the search
     *
     * @param class-string[] $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
    }


    public function register(): void
    {
        if (empty($this->attributes)) {
            $this->findAttributes();
        }

        $this->registerAttributes();
    }

    /**
     * Goes through every file in the directories listed and searches for any declared attributes.
     * Attributes declared via vendor packages, the IDE, or native PHP would not be found.
     *
     * @return class-string[]
     */
    protected function findAttributes(): array
    {
        $this->callableForClass(function(string $className) {
            $reflectionClass = new \ReflectionClass($className);
            $classAttributes = $reflectionClass->getAttributes();
            if (!empty($classAttributes) && $classAttributes[0]->getName() === 'Attribute') {
                $this->attributes[] = $className;
            }
        });

        return $this->attributes;
    }

    /**
     * Goes through every file in the directories listed and searches for discovered or specified attributes.
     * Searches for attributes targeting the class, the methods, and the properties
     *
     * @return void
     */
    protected function registerAttributes(): void
    {
        $this->callableForClass(function(string $className) {
            $this->addToCollection(AttributeTarget::TYPE_CLASS, $className, null, AttributeAccessor::forClass($className));

            $reflectionClass = new \ReflectionClass($className);

            foreach ($reflectionClass->getMethods() as $method) {
                $this->addToCollection(AttributeTarget::TYPE_METHOD, $className, $method->getName(), AttributeAccessor::forClassMethod($className, $method->getName()));
            }

            foreach ($reflectionClass->getProperties() as $property) {
                $this->addToCollection(AttributeTarget::TYPE_METHOD, $className, $property->getName(), AttributeAccessor::forClassProperty($className, $property->getName()));
            }
        });
    }

    /**
     * Loops through attributes and determines if they should be added to the collection
     *
     * @param int $type
     * @param string $className
     * @param string|null $identifier
     * @param AttributeInstance[]|null $attributeInstances
     * @return void
     */
    protected function addToCollection(int $type, string $className, ?string $identifier, ?array $attributeInstances): void
    {
        if ($attributeInstances === null) {
            return;
        }

        foreach($attributeInstances as $attributeInstance) {
            if (in_array($attributeInstance->name, $this->attributes)) {
                $this->collection->addInstance($type, $className, $identifier, $attributeInstance);
            }
        }
    }


    /**
     * Iterator function to handle looping through files and determining namespaces
     *
     * @param callable $function
     * @return void
     */
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

    /**
     * @return AttributeCollection
     */
    public function getAttributeCollection(): AttributeCollection
    {
        return $this->collection;
    }

}