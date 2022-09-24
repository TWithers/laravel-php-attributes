<?php

namespace TWithers\LaravelAttributes\Tests;

use Exception;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\SubDirectory\SubDirectoryClass;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\TestClass;

class AttributeRegistrarTest extends TestCase
{
    protected AttributeRegistrar $attributeRegistrar;

    public function setUp(): void
    {
        parent::setUp();
        $this->attributeRegistrar = new AttributeRegistrar;

        $this->attributeRegistrar->register();
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
    public function the_registrar_returns_an_attribute_collection()
    {
        $this->attributeRegistrar
            ->setAttributes(app('config')->get('attributes.attributes'))
            ->setDirectories(app('config')->get('attributes.directories'))
            ->register();

        $collection = $this->attributeRegistrar->getAttributeCollection();

        $this->assertInstanceOf(AttributeCollection::class, $collection);
    }

    /** @test */
    public function the_registrar_registers_class_attributes()
    {
        $this->attributeRegistrar
            ->setAttributes(app('config')->get('attributes.attributes'))
            ->setDirectories(app('config')->get('attributes.directories'))
            ->register();

        $collection = $this->attributeRegistrar->getAttributeCollection();

        $this->assertInstanceOf(\Countable::class, $collection);
        $this->assertCount(9, $this->attributeRegistrar->getAttributeCollection());
    }

    /** @test */
    public function the_registrar_handles_invalid_paths()
    {
        $exception = null;

        try {
            $this->attributeRegistrar
                ->setAttributes(app('config')->get('attributes.attributes'))
                ->setDirectories(['invalid_namespace' => 'invalid_path'])
                ->register();
        } catch (Exception $e) {
            $exception = $e;
        }
        $this->assertNotInstanceOf(DirectoryNotFoundException::class, $exception);
    }

    /** @test */
    public function the_registrar_handles_invalid_namespaces()
    {
        $exception = null;

        try {
            $this->attributeRegistrar
                ->setAttributes(app('config')->get('attributes.attributes'))
                ->setDirectories(['invalid_namespace' => __DIR__ . '/TestAttributes/Directory1'])
                ->register();
        } catch (Exception $e) {
            $exception = $e;
        }
        $this->assertNotInstanceOf(\ReflectionException::class, $exception);
    }

    /** @test */
    public function the_registrar_limits_attribute_lookups()
    {
        $this->attributeRegistrar
            ->setAttributes([TestClassAttribute::class])
            ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
            ->register();

        $collection = $this->attributeRegistrar->getAttributeCollection();
        $target = $collection->findByClass(TestClass::class);

        $this->assertCount(1, $target->allAttributes());
        $this->assertTrue($target->hasAttribute(TestClassAttribute::class));
        $this->assertFalse($target->hasAttribute(TestGenericAttribute::class));

        $this->attributeRegistrar
            ->setAttributes([TestClassAttribute::class, TestGenericAttribute::class])
            ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
            ->register();

        $collection = $this->attributeRegistrar->getAttributeCollection();
        $target = $collection->findByClass(TestClass::class);

        $this->assertCount(2, $target->allAttributes());
        $this->assertTrue($target->hasAttribute(TestClassAttribute::class));
        $this->assertTrue($target->hasAttribute(TestGenericAttribute::class));
    }

    /** @test */
    public function the_registrar_registers_method_attributes()
    {
        $this->attributeRegistrar
            ->setAttributes([TestGenericAttribute::class])
            ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
            ->register();

        $collection = $this->attributeRegistrar->getAttributeCollection();
        $target = $collection->findByClassMethod(TestClass::class, 'testMethod');

        $this->assertNotNull($target);
        $this->assertTrue($target->hasAttribute(TestGenericAttribute::class));
    }

    /** @test */
    public function the_registrar_registers_property_attributes()
    {
        $this->attributeRegistrar
            ->setAttributes([TestGenericAttribute::class])
            ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
            ->register();

        $collection = $this->attributeRegistrar->getAttributeCollection();
        $target = $collection->findByClassProperty(TestClass::class, 'public');

        $this->assertNotNull($target);
        $this->assertTrue($target->hasAttribute(TestGenericAttribute::class));

        $target = $collection->findByClassProperty(TestClass::class, 'protected');

        $this->assertNotNull($target);
        $this->assertTrue($target->hasAttribute(TestGenericAttribute::class));

        $target = $collection->findByClassProperty(TestClass::class, 'private');

        $this->assertNotNull($target);
        $this->assertTrue($target->hasAttribute(TestGenericAttribute::class));
    }

    /** @test */
    public function the_registrar_registers_subdirectories()
    {
        $this->attributeRegistrar
            ->setAttributes([TestGenericAttribute::class])
            ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
            ->register();

        $collection = $this->attributeRegistrar->getAttributeCollection();
        $target = $collection->findByClass(SubDirectoryClass::class);

        $this->assertNotNull($target);
        $this->assertTrue($target->hasAttribute(TestGenericAttribute::class));
    }

    /** @test */
    public function the_registrar_registers_repeatable_attributes()
    {
        $this->attributeRegistrar
            ->setAttributes([TestGenericAttribute::class])
            ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory2' => __DIR__ . '/TestAttributes/Directory2'])
            ->register();

        $collection = $this->attributeRegistrar->getAttributeCollection();
        $target = $collection->findByClass(\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class);

        $this->assertNotNull($target);
        $this->assertTrue($target->hasAttribute(TestGenericAttribute::class));
        $this->assertCount(2, $target->allAttributes());
    }
}
