<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\AttributeAccessor;
use TWithers\LaravelAttributes\AttributeRegistrar;
use TWithers\LaravelAttributes\AttributesServiceProvider;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\TestClass;

class ServiceProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->attributeRegistrar = app(AttributeRegistrar::class);
    }

    protected function getPackageProviders($app): array
    {
        return [
            AttributesServiceProvider::class,
        ];
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
    public function the_provider_can_register_the_accessor()
    {
        $this->assertInstanceOf(AttributeAccessor::class, app()->get(AttributeAccessor::class));
        $this->assertCount(12, app()->get(AttributeAccessor::class)->all());


        dump(app()->get('attributes')->whereClass(TestClass::class));
    }




}
