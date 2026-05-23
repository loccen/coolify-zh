<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::create([
            'uuid' => 'project',
            'name' => '我的第一个项目',
            'description' => '这是开发环境中的测试项目',
            'team_id' => 0,
        ]);

        // Update the auto-created environment with a deterministic UUID
        $project->environments()->first()->update([
            'uuid' => 'production',
            'name' => '生产',
        ]);
    }
}
