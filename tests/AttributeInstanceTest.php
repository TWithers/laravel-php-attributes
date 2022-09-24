<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\Attribute\AttributeAccessor;
use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\TestClass;

class AttributeInstanceTest extends TestCase
{


    /** @test */
    public function the_instance_constructor_works()
    {
        $instance = new AttributeInstance('mock', new \stdClass());
        $this->assertEquals('mock', $instance->name);
        $this->assertInstanceOf(\stdClass::class, $instance->instance);
    }

    /** @test */
    public function the_instance_can_be_an_array()
    {
        $instance = new AttributeInstance('mock', new \stdClass());
        $array = $instance->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertEquals('mock', $array['name']);
        $this->assertArrayHasKey('instance', $array);
        $this->assertInstanceOf(\stdClass::class, $array['instance']);
    }
}
