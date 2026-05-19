<?php

namespace Database\Seeders;

use App\Enums\ProxyStatus;
use App\Enums\ProxyTypes;
use App\Models\Server;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    public function run(): void
    {
        Server::create([
            'id' => 0,
            'uuid' => 'localhost',
            'name' => 'localhost',
            'description' => '这是开发模式下的测试 Docker 容器',
            'ip' => 'coolify-testing-host',
            'team_id' => 0,
            'private_key_id' => 1,
            'proxy' => [
                'type' => ProxyTypes::TRAEFIK->value,
                'status' => ProxyStatus::EXITED->value,
            ],
        ]);
    }
}
