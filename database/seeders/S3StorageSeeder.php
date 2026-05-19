<?php

namespace Database\Seeders;

use App\Models\S3Storage;
use Illuminate\Database\Seeder;

class S3StorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        S3Storage::create([
            'uuid' => 'minio',
            'name' => '本地 MinIO',
            'description' => '本地 MinIO S3 存储',
            'key' => 'minioadmin',
            'secret' => 'minioadmin',
            'bucket' => 'local',
            'endpoint' => 'http://coolify-minio:9000',
            'team_id' => 0,
            'is_usable' => true,
        ]);
    }
}
