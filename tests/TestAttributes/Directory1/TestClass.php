<?php

namespace TWithers\LaravelAttributes\Tests\TestAttributes\Directory1;

use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;

#[TestClassAttribute(name: 'class-name', label: 'class-label')]
#[TestGenericAttribute(name: 'generic-class-name', label: 'generic-class-label')]
class TestClass
{
    #[TestGenericAttribute(name: 'generic-public-property-name', label: 'generic-public-property-label')]
    public $public;

    #[TestGenericAttribute(name: 'generic-protected-property-name', label: 'generic-protected-property-label')]
    protected $protected;

    #[TestGenericAttribute(name: 'generic-private-property-name', label: 'generic-private-property-label')]
    private $private;

    #[TestMethodAttribute(name: 'method-name', label: 'method-label')]
    #[TestGenericAttribute(name: 'generic-method-name', label: 'generic-method-label')]
    public function testMethod(): bool
    {
        return true;
    }
}
