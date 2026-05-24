<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RootChangeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'root:change-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.root_change_email.description', locale: app()->getLocale()));
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $locale = app()->getLocale();

        $this->info(trans('console.root_change_email.about_to_change', locale: $locale));
        $email = $this->ask(trans('console.root_change_email.email_prompt', locale: $locale));
        $this->info(trans('console.root_change_email.updating', locale: $locale));
        try {
            $rootUser = User::find(0);

            if ($rootUser === null) {
                $this->error(trans('console.root_change_email.failed_to_update', locale: $locale));

                return self::FAILURE;
            }

            $rootUser->update(['email' => $email]);
            $this->info(trans('console.root_change_email.updated_successfully', locale: $locale));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error(trans('console.root_change_email.failed_to_update', locale: $locale));

            return self::FAILURE;
        }
    }
}
