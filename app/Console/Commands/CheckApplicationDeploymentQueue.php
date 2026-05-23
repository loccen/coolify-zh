<?php

namespace App\Console\Commands;

use App\Enums\ApplicationDeploymentStatus;
use App\Models\ApplicationDeploymentQueue;
use Illuminate\Console\Command;

class CheckApplicationDeploymentQueue extends Command
{
    protected $signature = 'check:deployment-queue {--force} {--seconds=3600}';

    protected $description = 'Check application deployment queue.';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.application_deployment_queue.description', locale: app()->getLocale()));
    }

    public function handle()
    {
        $seconds = $this->option('seconds');
        $deployments = ApplicationDeploymentQueue::whereIn('status', [
            ApplicationDeploymentStatus::IN_PROGRESS,
            ApplicationDeploymentStatus::QUEUED,
        ])->where('created_at', '<=', now()->subSeconds($seconds))->get();
        if ($deployments->isEmpty()) {
            $this->info(trans('console.application_deployment_queue.info.no_deployments_found', ['seconds' => $seconds], locale: app()->getLocale()));

            return;
        }

        $this->info(trans('console.application_deployment_queue.info.deployments_found', ['count' => $deployments->count(), 'seconds' => $seconds], locale: app()->getLocale()));

        foreach ($deployments as $deployment) {
            $message = trans('console.application_deployment_queue.info.deployment_is_stale', [
                'deployment_id' => $deployment->id,
                'created_at' => $deployment->created_at,
                'seconds' => $seconds,
            ], locale: app()->getLocale());

            if ($this->option('force')) {
                $this->info($message);
                $this->cancelDeployment($deployment);
            } else {
                $this->info($message);

                if ($this->confirm(trans('console.application_deployment_queue.confirm.cancel_deployment', [
                    'deployment_id' => $deployment->id,
                    'created_at' => $deployment->created_at,
                ], locale: app()->getLocale()), true)) {
                    $this->cancelDeployment($deployment);
                }
            }
        }
    }

    private function cancelDeployment(ApplicationDeploymentQueue $deployment)
    {
        $deployment->update(['status' => ApplicationDeploymentStatus::FAILED]);
        if ($deployment->server?->isFunctional()) {
            remote_process(['docker rm -f '.$deployment->deployment_uuid], $deployment->server, false);
        }
    }
}
