<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\Attribute\AttributeAccessor;
use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\TestClass;

class AttributeAccessorTest extends TestCase
{


    /** @test */
    public function the_accessor_returns_null_when_no_class_or_property_exists()
    {
        $accessor = new AttributeAccessor(AttributeTarget::TYPE_CLASS, 'classDoesntExist', null);
        $this->assertNull($accessor->reflectForAttributes());

        $accessor = new AttributeAccessor(AttributeTarget::TYPE_METHOD, TestClass::class, 'methodDoesntExist');
        $this->assertNull($accessor->reflectForAttributes());

        $accessor = new AttributeAccessor(AttributeTarget::TYPE_PROPERTY, TestClass::class, 'propertyDoesntExist');
        $this->assertNull($accessor->reflectForAttributes());
    }

    /** @test */
    public function the_accessor_returns_attributes_when_they_exist()
    {
        $accessor = new AttributeAccessor(AttributeTarget::TYPE_CLASS, TestClass::class, null);
        $this->assertNotNull($accessor->reflectForAttributes());
        $this->assertIsArray($accessor->reflectForAttributes());
        $this->assertCount(2, $accessor->reflectForAttributes());

        $accessor = new AttributeAccessor(AttributeTarget::TYPE_METHOD, TestClass::class, 'testMethod');
        $this->assertNotNull($accessor->reflectForAttributes());
        $this->assertIsArray($accessor->reflectForAttributes());
        $this->assertCount(2, $accessor->reflectForAttributes());

        $accessor = new AttributeAccessor(AttributeTarget::TYPE_PROPERTY, TestClass::class, 'public');
        $this->assertNotNull($accessor->reflectForAttributes());
        $this->assertIsArray($accessor->reflectForAttributes());
        $this->assertCount(1, $accessor->reflectForAttributes());
    }
}
