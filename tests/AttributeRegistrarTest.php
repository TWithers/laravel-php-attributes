<?php

namespace TWithers\LaravelAttributes\Tests;

use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;

class AttributeRegistrarTest extends TestCase
{
    protected AttributeRegistrar $attributeRegistrar;

    public function setUp(): void
    {
        parent::setUp();
        $this->attributeRegistrar = (new AttributeRegistrar)
            ->setAttributes(app('config')->get('attributes.attributes'))
            ->setDirectories(app('config')->get('attributes.directories'));
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

//    /** @test */
//    public function the_registrar_registers_class_attributes()
//    {
//        $map = $this->attributeRegistrar->getAttributeCollection();
//        $this->assertArrayHasKey(TestClass::class, $map);
//        $this->assertCount(2, $map[TestClass::class]);
//
//        $this->assertArrayHasKey(TestClassAttribute::class, $map[TestClass::class]);
//        $this->assertCount(1, $map[TestClass::class][TestClassAttribute::class]);
//
//        $this->assertArrayHasKey(TestGenericAttribute::class, $map[TestClass::class]);
//        $this->assertCount(1, $map[TestClass::class][TestGenericAttribute::class]);
//    }
//
//    /** @test */
//    public function the_registrar_registers_method_attributes()
//    {
//        $map = $this->attributeRegistrar->getAttributeMap();
//        $this->assertArrayHasKey(TestClass::class."::testMethod", $map);
//        $this->assertCount(2, $map[TestClass::class."::testMethod"]);
//
//        $this->assertArrayHasKey(TestMethodAttribute::class, $map[TestClass::class."::testMethod"]);
//        $this->assertCount(1, $map[TestClass::class."::testMethod"][TestMethodAttribute::class]);
//
//        $this->assertArrayHasKey(TestGenericAttribute::class, $map[TestClass::class."::testMethod"]);
//        $this->assertCount(1, $map[TestClass::class."::testMethod"][TestGenericAttribute::class]);
//    }
//
//    /** @test */
//    public function the_registrar_registers_subdirectories()
//    {
//        $map = $this->attributeRegistrar->getAttributeMap();
//        $this->assertArrayHasKey(SubDirectoryClass::class, $map);
//        $this->assertCount(2, $map[TestClass::class]);
//
//        $this->assertArrayHasKey(TestClassAttribute::class, $map[SubDirectoryClass::class]);
//        $this->assertCount(1, $map[SubDirectoryClass::class][TestClassAttribute::class]);
//
//        $this->assertArrayHasKey(TestGenericAttribute::class, $map[SubDirectoryClass::class]);
//        $this->assertCount(1, $map[SubDirectoryClass::class][TestGenericAttribute::class]);
//    }
//
//    /** @test */
//    public function the_registrar_registers_repeatable_attributes()
//    {
//        $map = $this->attributeRegistrar->getAttributeMap();
//        $this->assertArrayHasKey(\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class, $map);
//        $this->assertCount(2, $map[\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class]);
//
//        $this->assertArrayHasKey(TestGenericAttribute::class, $map[\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class]);
//        $this->assertCount(2, $map[\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class][TestGenericAttribute::class]);
//
//        $this->assertArrayHasKey(\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class."::testMethod", $map);
//        $this->assertCount(2, $map[\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class."::testMethod"]);
//
//        $this->assertArrayHasKey(TestGenericAttribute::class, $map[\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class."::testMethod"]);
//        $this->assertCount(2, $map[\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class."::testMethod"][TestGenericAttribute::class]);
//    }




}
