<?php

use App\Livewire\SettingsBackup;
use App\Models\ScheduledDatabaseBackup;

test('settings backup component stores backup as nullable model instead of array state', function () {
    $component = new SettingsBackup;
    $reflection = new ReflectionProperty(SettingsBackup::class, 'backup');
    $type = $reflection->getType();

    expect($component->backup)->toBeNull();
    expect($type)->toBeInstanceOf(ReflectionNamedType::class);
    expect($type->allowsNull())->toBeTrue();
    expect($type->getName())->toBe(ScheduledDatabaseBackup::class);
});
