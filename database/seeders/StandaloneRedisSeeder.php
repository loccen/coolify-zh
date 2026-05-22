<?php

namespace Database\Seeders;

use App\Models\StandaloneDocker;
use App\Models\StandaloneRedis;
use Illuminate\Database\Seeder;

class StandaloneRedisSeeder extends Seeder
{
    public function run(): void
    {
        StandaloneRedis::create([
            'name' => '本地 Redis',
            'description' => '用于测试的本地 Redis',
            'redis_password' => 'redis',
            'environment_id' => 1,
            'destination_id' => 0,
            'destination_type' => StandaloneDocker::class,
        ]);
    }
}
