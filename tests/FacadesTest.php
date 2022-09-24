<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\AttributesServiceProvider;
use TWithers\LaravelAttributes\Facades\Attributes;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;

class FacadesTest extends TestCase
{
    protected AttributesServiceProvider $attributesServiceProvider;

    public function setUp(): void
    {
        parent::setUp();
        if (file_exists(AttributesServiceProvider::getCachedAttributesPath())){
            unlink(AttributesServiceProvider::getCachedAttributesPath());
        }
        $this->attributesServiceProvider = new AttributesServiceProvider(app());
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

    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Attributes' => Attributes::class,
        ];
    }

    /**
     * @test
     */
    public function the_facade_works()
    {
        $this->assertIsArray(Attributes::all());
    }

}
