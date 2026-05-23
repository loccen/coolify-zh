<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'id' => 0,
            'name' => '根用户',
            'email' => 'test@example.com',
        ]);
        User::factory()->create([
            'id' => 1,
            'name' => '普通用户（属于根团队）',
            'email' => 'test2@example.com',
        ]);
        User::factory()->create([
            'id' => 2,
            'name' => '普通用户（不属于根团队）',
            'email' => 'test3@example.com',
        ]);
    }
}
