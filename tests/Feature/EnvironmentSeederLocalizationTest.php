<?php

use App\Models\Project;
use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps the seeded default project environment internal name as production', function () {
    $this->seed(ProjectSeeder::class);

    $project = Project::query()->where('uuid', 'project')->firstOrFail();
    $environment = $project->environments()->where('uuid', 'production')->firstOrFail();

    expect($environment->name)->toBe('production');
});
