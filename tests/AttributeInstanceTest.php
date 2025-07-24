<?php

use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;

test('the instance constructor works', function () {
    $instance = new AttributeInstance('mock', new \stdClass());
    expect($instance->name)->toBe('mock');
    expect($instance->instance)->toBeInstanceOf(\stdClass::class);
});

test('the instance can be an array', function () {
    $instance = new AttributeInstance('mock', new \stdClass());
    $array = $instance->toArray();

    expect($array)
        ->toHaveKey('name')
        ->and($array['name'])->toBe('mock')
        ->and($array)->toHaveKey('instance')
        ->and($array['instance'])->toBeInstanceOf(\stdClass::class);
});
