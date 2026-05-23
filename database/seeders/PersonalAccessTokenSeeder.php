<?php

namespace Database\Seeders;

use App\Models\PersonalAccessToken;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PersonalAccessTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only run in development environment
        if (app()->environment('production')) {
            $this->command->warn('Skipping PersonalAccessTokenSeeder in production environment');

            return;
        }

        // Get the first user (usually the admin user created during setup)
        $user = User::find(0);

        if (! $user) {
            $this->command->warn('No user found. Please run UserSeeder first.');

            return;
        }

        // Get the user's first team
        $team = $user->teams()->first();

        if (! $team) {
            $this->command->warn('No team found for user. Cannot create API tokens.');

            return;
        }

        // Define test tokens with different scopes
        $testTokens = [
            [
                'name' => '开发根权限令牌',
                'token' => 'root',
                'abilities' => ['root'],
            ],
            [
                'name' => '开发只读令牌',
                'token' => 'read',
                'abilities' => ['read'],
            ],
            [
                'name' => '开发敏感读取令牌',
                'token' => 'read-sensitive',
                'abilities' => ['read', 'read:sensitive'],
            ],
            [
                'name' => '开发写入令牌',
                'token' => 'write',
                'abilities' => ['write'],
            ],
            [
                'name' => '开发敏感写入令牌',
                'token' => 'write-sensitive',
                'abilities' => ['write', 'write:sensitive'],
            ],
            [
                'name' => '开发部署令牌',
                'token' => 'deploy',
                'abilities' => ['deploy'],
            ],
        ];

        // First, remove all existing development tokens for this user
        $deletedCount = PersonalAccessToken::where('tokenable_id', $user->id)
            ->where('tokenable_type', get_class($user))
            ->whereIn('name', array_column($testTokens, 'name'))
            ->delete();

        if ($deletedCount > 0) {
            $this->command->info("Removed {$deletedCount} existing development token(s).");
        }

        // Now create fresh tokens
        foreach ($testTokens as $tokenData) {
            // Create the token with a simple format: Bearer {scope}
            // The token format in the database is the hash of the plain text token
            $plainTextToken = $tokenData['token'];

            PersonalAccessToken::create([
                'tokenable_type' => get_class($user),
                'tokenable_id' => $user->id,
                'name' => $tokenData['name'],
                'token' => hash('sha256', $plainTextToken),
                'abilities' => $tokenData['abilities'],
                'team_id' => $team->id,
            ]);

            $this->command->info("Created token '{$tokenData['name']}' with Bearer token: {$plainTextToken}");
        }

        $this->command->info('');
        $this->command->info('开发环境 API 令牌已创建完成！');
        $this->command->info('你可以在开发环境中这样使用这些令牌：');
        $this->command->info('  Bearer root            - 根权限访问');
        $this->command->info('  Bearer read            - 只读访问');
        $this->command->info('  Bearer read-sensitive  - 敏感信息读取权限');
        $this->command->info('  Bearer write           - 写入权限');
        $this->command->info('  Bearer write-sensitive - 敏感信息写入权限');
        $this->command->info('  Bearer deploy          - 部署权限');
    }
}
