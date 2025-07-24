<?php

use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;

test('the target constructor works', function () {
    $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
    expect($target->type)->toBe(AttributeTarget::TYPE_CLASS);
    expect($target->className)->toBe('mock');
    expect($target->identifier)->toBeNull();
});

test('the target can be an array', function () {
    $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
    $array = $target->toArray();

    expect($array)
        ->toHaveKey('type')
        ->and($array['type'])->toBe(AttributeTarget::TYPE_CLASS)
        ->and($array)->toHaveKey('className')
        ->and($array['className'])->toBe('mock')
        ->and($array)->toHaveKey('identifier')
        ->and($array['identifier'])->toBeNull();
});

test('the target returns attribute instance array', function () {
    $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
    expect($target->allAttributes())
        ->toBeArray()
        ->toHaveCount(0);
});

test('the target can add attributes', function () {
    $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
    $target->addAttribute('mockAttributeInstance', new \stdClass());

    expect($target->allAttributes())
        ->toBeArray()
        ->toHaveCount(1)
        ->and($target->allAttributes()[0]->instance)->toBeInstanceOf(\stdClass::class);
});

test('the target can add attribute instances', function () {
    $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
    $attribute = new AttributeInstance('mockAttributeInstance', new \stdClass());
    $target->addAttribute($attribute);

    expect($target->allAttributes())
        ->toBeArray()
        ->toHaveCount(1)
        ->and($target->allAttributes()[0]->instance)->toBeInstanceOf(\stdClass::class);
});

test('the target can detect if attribute exists', function () {
    $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
    $attribute = new AttributeInstance('mockAttributeInstance', new \stdClass());

    expect($target->hasAttribute('mockAttributeInstance'))->toBeFalse();
    expect($target->hasAttribute('invalidMockAttributeInstance'))->toBeFalse();

    $target->addAttribute($attribute);

    expect($target->hasAttribute('mockAttributeInstance'))->toBeTrue();
    expect($target->hasAttribute('invalidMockAttributeInstance'))->toBeFalse();
});

test('the target can find attribute', function () {
    $target = new AttributeTarget(AttributeTarget::TYPE_CLASS, 'mock', null);
    $attribute = new AttributeInstance('mockAttributeInstance', new \stdClass());

    expect($target->findByName('mockAttributeInstance'))
        ->toBeArray()
        ->toBeEmpty();
    expect($target->findByName('invalidMockAttributeInstance'))
        ->toBeArray()
        ->toBeEmpty();

    $target->addAttribute($attribute);

    expect($target->findByName('mockAttributeInstance'))
        ->toBeArray()
        ->not()->toBeEmpty()
        ->and($target->findByName('mockAttributeInstance')[0])->toBeInstanceOf(AttributeInstance::class)
        ->and($target->findByName('mockAttributeInstance')[0])->toBe($attribute);

    expect($target->findByName('invalidMockAttributeInstance'))
        ->toBeArray()
        ->toBeEmpty();
});
