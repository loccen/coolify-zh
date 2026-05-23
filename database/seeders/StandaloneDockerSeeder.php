<?php

namespace Database\Seeders;

use App\Models\StandaloneDocker;
use Illuminate\Database\Seeder;

class StandaloneDockerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $standaloneDocker = StandaloneDocker::query()->firstOrCreate(
            [
                'server_id' => 0,
                'network' => 'coolify',
            ],
            [
                'uuid' => 'docker',
                'name' => '本地 Standalone Docker',
            ],
        );

        $standaloneDocker->name = '本地 Standalone Docker';
        $standaloneDocker->save();
    }
}
