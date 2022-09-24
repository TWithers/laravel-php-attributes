<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;
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

    /** @test */
    public function the_target_returns_attribute_instance_array()
    {
        $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
        $this->assertIsArray($target->allAttributes());
        $this->assertCount(0, $target->allAttributes());
    }

    /** @test */
    public function the_target_can_add_attributes()
    {
        $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
        $target->addAttribute('mockAttributeInstance', new \stdClass());

        $this->assertIsArray($target->allAttributes());
        $this->assertCount(1, $target->allAttributes());
        $this->assertInstanceOf(\stdClass::class, $target->allAttributes()[0]->instance);
    }

    /** @test */
    public function the_target_can_add_attribute_instances()
    {
        $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
        $attribute = new AttributeInstance('mockAttributeInstance', new \stdClass());
        $target->addAttribute($attribute);

        $this->assertIsArray($target->allAttributes());
        $this->assertCount(1, $target->allAttributes());
        $this->assertInstanceOf(\stdClass::class, $target->allAttributes()[0]->instance);
    }

    /** @test */
    public function the_target_can_detect_if_attribute_exists()
    {
        $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
        $attribute = new AttributeInstance('mockAttributeInstance', new \stdClass());

        $this->assertFalse($target->hasAttribute('mockAttributeInstance'));
        $this->assertFalse($target->hasAttribute('invalidMockAttributeInstance'));

        $target->addAttribute($attribute);

        $this->assertTrue($target->hasAttribute('mockAttributeInstance'));
        $this->assertFalse($target->hasAttribute('invalidMockAttributeInstance'));
    }

    /** @test */
    public function the_target_can_find_attribute()
    {
        $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
        $attribute = new AttributeInstance('mockAttributeInstance', new \stdClass());

        $this->assertIsArray($target->findByName('mockAttributeInstance'));
        $this->assertEmpty($target->findByName('mockAttributeInstance'));
        $this->assertIsArray($target->findByName('invalidMockAttributeInstance'));
        $this->assertEmpty($target->findByName('invalidMockAttributeInstance'));

        $target->addAttribute($attribute);

        $this->assertIsArray($target->findByName('mockAttributeInstance'));
        $this->assertNotEmpty($target->findByName('mockAttributeInstance'));
        $this->assertInstanceOf(AttributeInstance::class, $target->findByName('mockAttributeInstance')[0]);
        $this->assertSame($attribute, $target->findByName('mockAttributeInstance')[0]);

        $this->assertIsArray($target->findByName('invalidMockAttributeInstance'));
        $this->assertEmpty($target->findByName('invalidMockAttributeInstance'));
    }
}
