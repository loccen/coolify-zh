<?php

use App\Console\Commands\Seeder;
use Illuminate\Support\Facades\App;

it('wires the seeder command strings through console translations', function () {
    $command = file_get_contents(app_path('Console/Commands/Seeder.php'));

    expect($command)
        ->toContain("trans('console.seeder.description'")
        ->toContain("trans('console.seeder.enabled'")
        ->toContain("trans('console.seeder.disabled'");
});

it('resolves seeder console translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.seeder.description'))->toBe('Start Seeder')
        ->and(trans('console.seeder.enabled'))->toBe('Seeder is enabled on this server.')
        ->and(trans('console.seeder.disabled'))->toBe('Seeder is disabled on this server.')
        ->and((new Seeder)->getDescription())->toBe('Start Seeder');

    App::setLocale('zh_CN');

    expect(trans('console.seeder.description'))->toBe('开始 Seeder')
        ->and(trans('console.seeder.enabled'))->toBe('本服务器已启用 Seeder。')
        ->and(trans('console.seeder.disabled'))->toBe('本服务器未启用 Seeder。')
        ->and((new Seeder)->getDescription())->toBe('开始 Seeder');
});
