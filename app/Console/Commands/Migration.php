<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Migration extends Command
{
    protected $signature = 'start:migration';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.migration.description', locale: app()->getLocale()));
    }

    public function handle()
    {
        if (config('constants.migration.is_migration_enabled')) {
            $this->info(trans('console.migration.enabled', locale: app()->getLocale()));
            $this->call('migrate', ['--force' => true, '--isolated' => true]);
            exit(0);
        } else {
            $this->info(trans('console.migration.disabled', locale: app()->getLocale()));
            exit(0);
        }
    }
}
