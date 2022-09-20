<?php

namespace TWithers\LaravelAttributes\Tests\TestAttributes\Directory1;

use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;

#[TestClassAttribute(name: 'class-name', label: 'class-label')]
#[TestGenericAttribute(name: 'generic-class-name', label: 'generic-class-label')]
class TestClass
{
    #[TestMethodAttribute(name: 'method-name', label: 'method-label')]
    #[TestGenericAttribute(name: 'generic-method-name', label: 'generic-method-label')]
    public function testMethod(): bool
    {
        return true;
    }
}