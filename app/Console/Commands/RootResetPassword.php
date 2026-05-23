<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;

class RootResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'root:reset-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function __construct()
    {
        parent::__construct();

        $this->description = trans('console.root_reset_password.description', locale: app()->getLocale());
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = app()->getLocale();

        $this->info(trans('console.root_reset_password.about_to_reset', locale: $locale));
        $password = password(trans('console.root_reset_password.password_prompt', locale: $locale));
        $passwordAgain = password(trans('console.root_reset_password.password_again_prompt', locale: $locale));
        if ($password != $passwordAgain) {
            $this->error(trans('console.root_reset_password.passwords_do_not_match', locale: $locale));

            return;
        }
        $this->info(trans('console.root_reset_password.updating', locale: $locale));
        try {
            $user = User::find(0);
            if (! $user) {
                $this->error(trans('console.root_reset_password.root_user_not_found', locale: $locale));

                return;
            }
            $user->update(['password' => Hash::make($password)]);
            $this->info(trans('console.root_reset_password.updated_successfully', locale: $locale));
        } catch (\Exception $e) {
            $this->error(trans('console.root_reset_password.failed_to_update', locale: $locale));

            return;
        }
    }
}
