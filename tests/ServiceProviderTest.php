<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\AttributesServiceProvider;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;

class ServiceProviderTest extends TestCase
{
    protected AttributesServiceProvider $attributesServiceProvider;

    public function setUp(): void
    {
        parent::setUp();
        if (file_exists(AttributesServiceProvider::getCachedAttributesPath())) {
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

    protected function usesDefaultConfig($app)
    {
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

    protected function usesCache($app)
    {
        $app['config']->set('attributes.use_cache', true);
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
     * @test
     * @define-env usesDefaultConfig
     */
    public function the_provider_can_register_the_accessor()
    {
        $this->assertInstanceOf(AttributeCollection::class, app()->get(AttributeCollection::class));
        $this->assertInstanceOf(AttributeCollection::class, app()->get('attributes'));
    }

    /**
     * @test
     * @define-env usesCache
     */
    public function the_provider_uses_cache()
    {
        $this->assertFileDoesNotExist(AttributesServiceProvider::getCachedAttributesPath());
        $this->attributesServiceProvider->boot();
        $this->assertFileExists(AttributesServiceProvider::getCachedAttributesPath());
    }

    /**
     * @test
     * @define-env usesCache
     */
    public function the_provider_handles_invalid_cache()
    {
        $contents = "<?php return 'notvalidserialized';";
        file_put_contents(AttributesServiceProvider::getCachedAttributesPath(), $contents);
        $this->attributesServiceProvider->boot();
        $this->assertNotSame($contents, file_get_contents(AttributesServiceProvider::getCachedAttributesPath()));
    }

    /**
     * @test
     * @define-env usesDefaultConfig
     */
    public function the_provider_can_disable_cache()
    {
        $this->assertFileDoesNotExist(AttributesServiceProvider::getCachedAttributesPath());
        $attributesServiceProvider = new AttributesServiceProvider(app());
        $attributesServiceProvider->boot();
        $this->assertFileDoesNotExist(AttributesServiceProvider::getCachedAttributesPath());
    }
}
