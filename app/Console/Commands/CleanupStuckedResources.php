<?php

namespace App\Console\Commands;

use App\Jobs\CleanupHelperContainersJob;
use App\Jobs\DeleteResourceJob;
use App\Models\Application;
use App\Models\ApplicationDeploymentQueue;
use App\Models\ApplicationPreview;
use App\Models\ScheduledDatabaseBackup;
use App\Models\ScheduledTask;
use App\Models\Server;
use App\Models\Service;
use App\Models\ServiceApplication;
use App\Models\ServiceDatabase;
use App\Models\SslCertificate;
use App\Models\StandaloneClickhouse;
use App\Models\StandaloneDragonfly;
use App\Models\StandaloneKeydb;
use App\Models\StandaloneMariadb;
use App\Models\StandaloneMongodb;
use App\Models\StandaloneMysql;
use App\Models\StandalonePostgresql;
use App\Models\StandaloneRedis;
use App\Models\Team;
use Illuminate\Console\Command;

class CleanupStuckedResources extends Command
{
    protected $signature = 'cleanup:stucked-resources';

    protected $description = 'Cleanup Stucked Resources';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.cleanup_stucked_resources.description', locale: app()->getLocale()));
    }

    public function handle()
    {
        $this->cleanup_stucked_resources();
    }

    private function cleanup_stucked_resources()
    {
        try {
            $teams = Team::all()->filter(function ($team) {
                return $team->members()->count() === 0 && $team->servers()->count() === 0;
            });
            foreach ($teams as $team) {
                $team->delete();
            }
            $servers = Server::all()->filter(function ($server) {
                return $server->isFunctional();
            });
            if (isCloud()) {
                $servers = $servers->filter(function ($server) {
                    return data_get($server->team->subscription, 'stripe_invoice_paid', false) === true;
                });
            }
            foreach ($servers as $server) {
                CleanupHelperContainersJob::dispatch($server);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stucked_resources', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $servers = Server::onlyTrashed()->get();
            foreach ($servers as $server) {
                echo trans('console.cleanup_stucked_resources.info.force_deleting_stuck_server', ['name' => $server->name], locale: app()->getLocale())."\n";
                $server->forceDelete();
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_servers', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $applicationsDeploymentQueue = ApplicationDeploymentQueue::get();
            foreach ($applicationsDeploymentQueue as $applicationDeploymentQueue) {
                if (is_null($applicationDeploymentQueue->application)) {
                    echo trans('console.cleanup_stucked_resources.info.deleting_stuck_application_deployment_queue', ['id' => $applicationDeploymentQueue->id], locale: app()->getLocale())."\n";
                    $applicationDeploymentQueue->delete();
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_application_deployment_queue', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $applications = Application::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($applications as $application) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_application', ['name' => $application->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($application);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_application', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $applicationsPreviews = ApplicationPreview::get();
            foreach ($applicationsPreviews as $applicationPreview) {
                if (! data_get($applicationPreview, 'application')) {
                    echo trans('console.cleanup_stucked_resources.info.deleting_stuck_application_preview', ['identifier' => $applicationPreview->uuid], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($applicationPreview);
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_application', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $applicationsPreviews = ApplicationPreview::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($applicationsPreviews as $applicationPreview) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_application_preview', ['identifier' => $applicationPreview->fqdn], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($applicationPreview);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_application', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $postgresqls = StandalonePostgresql::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($postgresqls as $postgresql) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_postgresql', ['name' => $postgresql->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($postgresql);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_postgresql', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $rediss = StandaloneRedis::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($rediss as $redis) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_redis', ['name' => $redis->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($redis);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_redis', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $keydbs = StandaloneKeydb::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($keydbs as $keydb) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_keydb', ['name' => $keydb->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($keydb);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_keydb', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $dragonflies = StandaloneDragonfly::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($dragonflies as $dragonfly) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_dragonfly', ['name' => $dragonfly->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($dragonfly);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_dragonfly', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $clickhouses = StandaloneClickhouse::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($clickhouses as $clickhouse) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_clickhouse', ['name' => $clickhouse->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($clickhouse);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_clickhouse', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $mongodbs = StandaloneMongodb::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($mongodbs as $mongodb) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_mongodb', ['name' => $mongodb->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($mongodb);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_mongodb', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $mysqls = StandaloneMysql::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($mysqls as $mysql) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_mysql', ['name' => $mysql->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($mysql);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_mysql', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $mariadbs = StandaloneMariadb::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($mariadbs as $mariadb) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_mariadb', ['name' => $mariadb->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($mariadb);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_mariadb', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $services = Service::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($services as $service) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_service', ['name' => $service->name], locale: app()->getLocale())."\n";
                DeleteResourceJob::dispatch($service);
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_service', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $serviceApps = ServiceApplication::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($serviceApps as $serviceApp) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_serviceapp', ['name' => $serviceApp->name], locale: app()->getLocale())."\n";
                $serviceApp->forceDelete();
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_serviceapp', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $serviceDbs = ServiceDatabase::withTrashed()->whereNotNull('deleted_at')->get();
            foreach ($serviceDbs as $serviceDb) {
                echo trans('console.cleanup_stucked_resources.info.deleting_stuck_serviceapp', ['name' => $serviceDb->name], locale: app()->getLocale())."\n";
                $serviceDb->forceDelete();
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_serviceapp', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $scheduled_tasks = ScheduledTask::all();
            foreach ($scheduled_tasks as $scheduled_task) {
                if (! $scheduled_task->service && ! $scheduled_task->application) {
                    echo trans('console.cleanup_stucked_resources.info.deleting_stuck_scheduledtask', ['name' => $scheduled_task->name], locale: app()->getLocale())."\n";
                    $scheduled_task->delete();
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_scheduledtasks', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }

        try {
            $scheduled_backups = ScheduledDatabaseBackup::all();
            foreach ($scheduled_backups as $scheduled_backup) {
                try {
                    $server = $scheduled_backup->server();
                    if (! $server) {
                        echo trans('console.cleanup_stucked_resources.info.deleting_stuck_scheduledbackup', ['name' => $scheduled_backup->name], locale: app()->getLocale())."\n";
                        $scheduled_backup->delete();
                    }
                } catch (\Throwable $e) {
                    echo trans('console.cleanup_stucked_resources.error.error_checking_server_for_scheduledbackup', ['id' => $scheduled_backup->id, 'error' => $e->getMessage()], locale: app()->getLocale())."\n";
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_stuck_scheduledbackups', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }

        // Cleanup any resources that are not attached to any environment or destination or server
        try {
            $applications = Application::all();
            foreach ($applications as $application) {
                if (! data_get($application, 'environment')) {
                    echo trans('console.cleanup_stucked_resources.info.application_without_environment', ['name' => $application->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($application);

                    continue;
                }
                if (! $application->destination()) {
                    echo trans('console.cleanup_stucked_resources.info.application_without_destination', ['name' => $application->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($application);

                    continue;
                }
                if (! data_get($application, 'destination.server')) {
                    echo trans('console.cleanup_stucked_resources.info.application_without_server', ['name' => $application->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($application);

                    continue;
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_application', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $postgresqls = StandalonePostgresql::all()->where('id', '!=', 0);
            foreach ($postgresqls as $postgresql) {
                if (! data_get($postgresql, 'environment')) {
                    echo trans('console.cleanup_stucked_resources.info.postgresql_without_environment', ['name' => $postgresql->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($postgresql);

                    continue;
                }
                if (! $postgresql->destination()) {
                    echo trans('console.cleanup_stucked_resources.info.postgresql_without_destination', ['name' => $postgresql->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($postgresql);

                    continue;
                }
                if (! data_get($postgresql, 'destination.server')) {
                    echo trans('console.cleanup_stucked_resources.info.postgresql_without_server', ['name' => $postgresql->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($postgresql);

                    continue;
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_postgresql', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $redis = StandaloneRedis::all();
            foreach ($redis as $redis) {
                if (! data_get($redis, 'environment')) {
                    echo trans('console.cleanup_stucked_resources.info.redis_without_environment', ['name' => $redis->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($redis);

                    continue;
                }
                if (! $redis->destination()) {
                    echo trans('console.cleanup_stucked_resources.info.redis_without_destination', ['name' => $redis->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($redis);

                    continue;
                }
                if (! data_get($redis, 'destination.server')) {
                    echo trans('console.cleanup_stucked_resources.info.redis_without_server', ['name' => $redis->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($redis);

                    continue;
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_redis', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }

        try {
            $mongodbs = StandaloneMongodb::all();
            foreach ($mongodbs as $mongodb) {
                if (! data_get($mongodb, 'environment')) {
                    echo trans('console.cleanup_stucked_resources.info.mongodb_without_environment', ['name' => $mongodb->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($mongodb);

                    continue;
                }
                if (! $mongodb->destination()) {
                    echo trans('console.cleanup_stucked_resources.info.mongodb_without_destination', ['name' => $mongodb->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($mongodb);

                    continue;
                }
                if (! data_get($mongodb, 'destination.server')) {
                    echo trans('console.cleanup_stucked_resources.info.mongodb_without_server', ['name' => $mongodb->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($mongodb);

                    continue;
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_mongodb', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }

        try {
            $mysqls = StandaloneMysql::all();
            foreach ($mysqls as $mysql) {
                if (! data_get($mysql, 'environment')) {
                    echo trans('console.cleanup_stucked_resources.info.mysql_without_environment', ['name' => $mysql->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($mysql);

                    continue;
                }
                if (! $mysql->destination()) {
                    echo trans('console.cleanup_stucked_resources.info.mysql_without_destination', ['name' => $mysql->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($mysql);

                    continue;
                }
                if (! data_get($mysql, 'destination.server')) {
                    echo trans('console.cleanup_stucked_resources.info.mysql_without_server', ['name' => $mysql->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($mysql);

                    continue;
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_mysql', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }

        try {
            $mariadbs = StandaloneMariadb::all();
            foreach ($mariadbs as $mariadb) {
                if (! data_get($mariadb, 'environment')) {
                    echo trans('console.cleanup_stucked_resources.info.mariadb_without_environment', ['name' => $mariadb->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($mariadb);

                    continue;
                }
                if (! $mariadb->destination()) {
                    echo trans('console.cleanup_stucked_resources.info.mariadb_without_destination', ['name' => $mariadb->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($mariadb);

                    continue;
                }
                if (! data_get($mariadb, 'destination.server')) {
                    echo trans('console.cleanup_stucked_resources.info.mariadb_without_server', ['name' => $mariadb->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($mariadb);

                    continue;
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_mariadb', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }

        try {
            $services = Service::all();
            foreach ($services as $service) {
                if (! data_get($service, 'environment')) {
                    echo trans('console.cleanup_stucked_resources.info.service_without_environment', ['name' => $service->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($service);

                    continue;
                }
                if (! $service->destination()) {
                    echo trans('console.cleanup_stucked_resources.info.service_without_destination', ['name' => $service->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($service);

                    continue;
                }
                if (! data_get($service, 'server')) {
                    echo trans('console.cleanup_stucked_resources.info.service_without_server', ['name' => $service->name], locale: app()->getLocale())."\n";
                    DeleteResourceJob::dispatch($service);

                    continue;
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_service', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $serviceApplications = ServiceApplication::all();
            foreach ($serviceApplications as $service) {
                if (! data_get($service, 'service')) {
                    echo trans('console.cleanup_stucked_resources.info.service_application_without_service', ['name' => $service->name], locale: app()->getLocale())."\n";
                    $service->forceDelete();

                    continue;
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_service_applications', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
        try {
            $serviceDatabases = ServiceDatabase::all();
            foreach ($serviceDatabases as $service) {
                if (! data_get($service, 'service')) {
                    echo trans('console.cleanup_stucked_resources.info.service_database_without_service', ['name' => $service->name], locale: app()->getLocale())."\n";
                    $service->forceDelete();

                    continue;
                }
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_service_databases', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }

        try {
            $orphanedCerts = SslCertificate::whereNotIn('server_id', function ($query) {
                $query->select('id')->from('servers');
            })->get();

            foreach ($orphanedCerts as $cert) {
                echo trans('console.cleanup_stucked_resources.info.deleting_orphaned_ssl_certificate', ['id' => $cert->id, 'server_id' => $cert->server_id], locale: app()->getLocale())."\n";
                $cert->delete();
            }
        } catch (\Throwable $e) {
            echo trans('console.cleanup_stucked_resources.error.error_in_cleaning_orphaned_ssl_certificates', ['error' => $e->getMessage()], locale: app()->getLocale())."\n";
        }
    }
}
