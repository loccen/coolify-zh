<?php

namespace Database\Seeders;

use App\Models\InstanceSettings;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RootUserSeeder extends Seeder
{
    public function run(): void
    {
        try {
            if (User::where('id', 0)->exists()) {
                echo "\n  INFO  Root user already exists. Skipping creation.\n\n";

                return;
            }

            if (! env('ROOT_USER_EMAIL') || ! env('ROOT_USER_PASSWORD')) {
                return;
            }

            $validator = Validator::make([
                'email' => env('ROOT_USER_EMAIL'),
                'username' => env('ROOT_USERNAME', '根用户'),
                'password' => env('ROOT_USER_PASSWORD'),
            ], [
                'email' => ['required', 'email:rfc,dns', 'max:255'],
                'username' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\p{L}\p{N}\s_-]+$/u'],
                'password' => ['required', 'string', 'min:8', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            ]);

            if ($validator->fails()) {
                echo "\n  ERROR  根用户环境变量无效\n";
                foreach ($validator->errors()->all() as $error) {
                    echo "  → {$error}\n";
                }
                echo "\n";

                return;
            }

            try {
                $user = (new User)->forceFill([
                    'id' => 0,
                    'name' => env('ROOT_USERNAME', '根用户'),
                    'email' => env('ROOT_USER_EMAIL'),
                    'password' => Hash::make(env('ROOT_USER_PASSWORD')),
                ]);
                $user->save();
                echo "\n  SUCCESS  根用户创建成功。\n\n";
            } catch (\Exception $e) {
                echo "\n  ERROR  创建根用户失败：{$e->getMessage()}\n\n";

                return;
            }

            try {
                InstanceSettings::updateOrCreate(
                    ['id' => 0],
                    ['is_registration_enabled' => false]
                );
                echo "\n  SUCCESS  已成功关闭注册功能。\n\n";
            } catch (\Exception $e) {
                echo "\n  ERROR  更新实例设置失败：{$e->getMessage()}\n\n";
            }
        } catch (\Exception $e) {
            echo "\n  ERROR  发生未预期错误：{$e->getMessage()}\n\n";
        }
    }
}
