<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Seeder extends Command
{
    protected $signature = 'start:seeder';

    protected $description = 'Start Seeder';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.seeder.description', locale: app()->getLocale()));
    }

    public function handle()
    {
        if (config('constants.seeder.is_seeder_enabled')) {
            $this->info(trans('console.seeder.enabled', locale: app()->getLocale()));
            $this->call('db:seed', ['--class' => 'ProductionSeeder', '--force' => true]);
            exit(0);
        } else {
            $this->info(trans('console.seeder.disabled', locale: app()->getLocale()));
            exit(0);
        }
    }
}
