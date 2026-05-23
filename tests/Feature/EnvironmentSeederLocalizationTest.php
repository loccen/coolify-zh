<?php

use App\Models\Project;
use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds the default project environment name in chinese for local review flows', function () {
    $this->seed(ProjectSeeder::class);

    $project = Project::query()->where('uuid', 'project')->firstOrFail();
    $environment = $project->environments()->where('uuid', 'production')->firstOrFail();

    expect($environment->name)->toBe('生产');
});
