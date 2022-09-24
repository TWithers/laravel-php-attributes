<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;

class AttributeTargetTest extends TestCase
{
    /** @test */
    public function the_target_constructor_works()
    {
        $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
        $this->assertEquals(AttributeTarget::TYPE_CLASS, $target->type);
        $this->assertEquals('mock', $target->className);
        $this->assertNull($target->identifier);
    }

    /** @test */
    public function the_target_can_be_an_array()
    {
        $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
        $array = $target->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertEquals(AttributeTarget::TYPE_CLASS, $array['type']);
        $this->assertArrayHasKey('className', $array);
        $this->assertEquals('mock', $array['className']);
        $this->assertArrayHasKey('identifier', $array);
        $this->assertNull($array['identifier']);
    }
}
