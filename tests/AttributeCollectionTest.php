<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\TestClass;

class AttributeCollectionTest extends TestCase
{
    protected AttributeCollection $attributeCollection;

    public function setUp(): void
    {
        parent::setUp();
        $attributeRegistrar = (new AttributeRegistrar())
            ->setAttributes(app('config')->get('attributes.attributes'))
            ->setDirectories(app('config')->get('attributes.directories'));
        $attributeRegistrar->register();
        $this->attributeCollection = $attributeRegistrar->getAttributeCollection();
    }

    /**
     * Resolve application core configuration implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function resolveApplicationConfiguration($app): void
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('attributes.use_cache', false);
        $app['config']->set('attributes.directories', [
            'TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1',
            'TWithers\LaravelAttributes\Tests\TestAttributes\Directory2' => __DIR__ . '/TestAttributes/Directory2',
        ]);
        $app['config']->set('attributes.attributes', [
            TestClassAttribute::class,
            TestGenericAttribute::class,
            TestMethodAttribute::class,
        ]);
    }

    /** @test */
    public function the_collection_is_countable()
    {
        $this->assertInstanceOf(\Countable::class, $this->attributeCollection);
    }

    /** @test */
    public function the_collection_is_populated_by_registrar()
    {
        $this->assertCount(9, $this->attributeCollection->all());
    }

    /** @test */
    public function the_collection_can_find_an_attribute()
    {
        $this->assertInstanceOf(
            TestClassAttribute::class,
            $this->attributeCollection->find(AttributeTarget::TYPE_CLASS, TestClass::class, null)->allAttributes()[0]->instance
        );
    }

    /** @test */
    public function the_collection_can_find_an_attribute_by_class()
    {
        $this->assertInstanceOf(
            TestClassAttribute::class,
            $this->attributeCollection->findByClass(TestClass::class)->allAttributes()[0]->instance
        );
    }

    /** @test */
    public function the_collection_can_find_an_attribute_by_method()
    {
        $this->assertInstanceOf(
            TestMethodAttribute::class,
            $this->attributeCollection->findByClassMethod(TestClass::class, 'testMethod')->allAttributes()[0]->instance
        );
    }

    /** @test */
    public function the_collection_can_find_an_attribute_by_property()
    {
        $this->assertInstanceOf(
            TestGenericAttribute::class,
            $this->attributeCollection->findByClassProperty(TestClass::class, 'public')->allAttributes()[0]->instance
        );
    }

    /** @test */
    public function the_collection_handles_serialization()
    {
        $serialized = serialize($this->attributeCollection);
        $this->assertIsString($serialized);

        $newAttributeCollection = unserialize($serialized);

        $this->assertInstanceOf(
            AttributeCollection::class,
            $newAttributeCollection
        );
        $this->assertEquals($this->attributeCollection, $newAttributeCollection);
        $this->assertCount(count($this->attributeCollection), $newAttributeCollection);
    }

    /** @test */
    public function the_collection_can_be_added_to()
    {
        $originalCount = count($this->attributeCollection);
        $this->attributeCollection->add(AttributeTarget::TYPE_CLASS, 'newMockClass', null, \stdClass::class, new \stdClass());
        $this->assertCount($originalCount + 1, $this->attributeCollection);

        $collection = $this->attributeCollection->all();
        $target = end($collection);

        $this->assertInstanceOf(AttributeTarget::class, $target);
        $this->assertTrue($target->hasAttribute(\stdClass::class));
        $this->assertInstanceOf(\stdClass::class, $target->findByName(\stdClass::class)[0]->instance);
    }

    /** @test */
    public function the_collection_can_be_added_to_with_attribute_instances()
    {
        $originalCount = count($this->attributeCollection);
        $attributeInstance = new AttributeInstance('mockInstance', new \stdClass());
        $this->attributeCollection->addInstance(AttributeTarget::TYPE_CLASS, 'newMockClass', null, $attributeInstance);
        $this->assertCount($originalCount + 1, $this->attributeCollection);

        $collection = $this->attributeCollection->all();
        $target = end($collection);

        $this->assertInstanceOf(AttributeTarget::class, $target);
        $this->assertTrue($target->hasAttribute('mockInstance'));
        $this->assertInstanceOf(\stdClass::class, $target->findByName('mockInstance')[0]->instance);
    }
}
