<?php

namespace TWithers\LaravelAttributes\Tests\TestAttributes\Directory2;

use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;

#[TestClassAttribute(name: 'class-name', label: 'class-label')]
#[TestGenericAttribute(name: 'generic-class-name', label: 'generic-class-label')]
#[TestGenericAttribute(name: 'generic-class-name-duplicate', label: 'generic-class-label-duplicate')]
class TestClass
{
    #[TestMethodAttribute(name: 'method-name', label: 'method-label')]
    #[TestGenericAttribute(name: 'generic-method-name', label: 'generic-method-label')]
    #[TestGenericAttribute(name: 'generic-method-name-duplicate', label: 'generic-method-label-duplicate')]
    public function testMethod(): bool
    {
        return true;
    }
}