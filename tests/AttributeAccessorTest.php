<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\AttributeAccessor;
use TWithers\LaravelAttributes\AttributeRegistrar;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\SubDirectory\SubDirectoryClass;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\TestClass;

class AttributeAccessorTest extends TestCase
{
    protected AttributeAccessor $attributeAccessor;

    public function setUp(): void
    {
        parent::setUp();
        $attributeRegistrar = (new AttributeRegistrar)
            ->setAttributes(app('config')->get('attributes.attributes'))
            ->setDirectories(app('config')->get('attributes.directories'));
        $attributeRegistrar->register();
        $this->attributeAccessor = new AttributeAccessor($attributeRegistrar->getAttributeMap());

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

        $app['config']->set('attributes.use_cache', [true]);
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
    public function the_accessor_populates_the_map()
    {
        $this->assertCount(14, $this->attributeAccessor->all());
    }

    /** @test */
    public function the_accessor_can_return_an_attribute_instance()
    {
        $this->assertInstanceOf(TestClassAttribute::class, $this->attributeAccessor->getInstance(TestClass::class, TestClassAttribute::class));
    }

    /** @test */
    public function the_accessor_items_are_correct()
    {
        $this->assertArrayHasKey('class', $this->attributeAccessor->get(0));
        $this->assertArrayHasKey('method', $this->attributeAccessor->get(0));
        $this->assertArrayHasKey('attribute', $this->attributeAccessor->get(0));
        $this->assertArrayHasKey('instance', $this->attributeAccessor->get(0));
    }




}
