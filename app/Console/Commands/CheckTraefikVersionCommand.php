<?php

namespace App\Console\Commands;

use App\Jobs\CheckTraefikVersionJob;
use Illuminate\Console\Command;

class CheckTraefikVersionCommand extends Command
{
    protected $signature = 'traefik:check-version';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.traefik_check_version.description', locale: app()->getLocale()));
    }

    public function handle(): int
    {
        $this->info(trans('console.traefik_check_version.checking', locale: app()->getLocale()));

        try {
            CheckTraefikVersionJob::dispatch();
            $this->info(trans('console.traefik_check_version.dispatched', locale: app()->getLocale()));
            $this->info(trans('console.traefik_check_version.notifications_pending', locale: app()->getLocale()));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error(trans('console.traefik_check_version.dispatch_failed', ['message' => $e->getMessage()], locale: app()->getLocale()));

            return Command::FAILURE;
        }
    }
}
