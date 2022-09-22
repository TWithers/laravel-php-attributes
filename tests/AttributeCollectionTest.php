<?php

namespace TWithers\LaravelAttributes\Tests;


use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
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

        $app['config']->set('attributes.use_cache', [false]);
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





}
