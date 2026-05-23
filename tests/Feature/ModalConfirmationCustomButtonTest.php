<?php

it('supports custom button slots without translating the slot object', function () {
    $component = file_get_contents(base_path('resources/views/components/modal-confirmation.blade.php'));

    expect($component)
        ->toContain('$customButton instanceof \\Illuminate\\View\\ComponentSlot')
        ->toContain('$customButtonContent = $customButton instanceof \\Illuminate\\View\\ComponentSlot ? $customButton : __($customButton);')
        ->toContain('{{ $customButtonContent }}');
});
