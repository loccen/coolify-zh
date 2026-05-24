<?php

use App\Models\PersonalAccessToken;
use Database\Seeders\PersonalAccessTokenSeeder;
use Database\Seeders\TeamSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds development personal access tokens with chinese display names', function () {
    $this->seed(UserSeeder::class);
    $this->seed(TeamSeeder::class);
    $this->seed(PersonalAccessTokenSeeder::class);

    $names = PersonalAccessToken::query()
        ->pluck('name')
        ->all();

    expect($names)->toContain(
        '开发根权限令牌',
        '开发只读令牌',
        '开发敏感读取令牌',
        '开发写入令牌',
        '开发敏感写入令牌',
        '开发部署令牌',
    );
});
