<?php

namespace TWithers\LaravelAttributes;

use Illuminate\Support\Traits\ForwardsCalls;

class AttributeAccessor
{
    use ForwardsCalls;

    protected AttributeCollection $attributeMap;

    public function __construct(array $attributeMap = [])
    {
        $this->setAttributeMap($attributeMap);
    }

    public function setAttributeMap(array $attributeMap): void
    {
        $attributeCollection = [];
        foreach ($attributeMap as $className => $attributeArray) {
            foreach ($attributeArray as $attributeName => $attributes) {
                foreach ($attributes as $attribute) {
                    $method = null;
                    $class = null;
                    if (str_contains($className, "::")) {
                        $method = $className;
                    } else {
                        $class = $className;
                    }
                    $item = [
                        'class' => $class,
                        'method' => $method,
                        'attribute' => $attributeName,
                        'instance' => unserialize($attribute)
                    ];
                    $attributeCollection[] = $item;
                }
            }
        }
        $this->attributeMap = new AttributeCollection($attributeCollection);
    }

    /**
     * Pass dynamic methods onto the router instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo(
            $this->attributeMap, $method, $parameters
        );
    }
}
