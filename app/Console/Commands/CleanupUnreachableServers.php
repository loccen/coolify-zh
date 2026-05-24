<?php

namespace App\Console\Commands;

use App\Models\Server;
use Illuminate\Console\Command;

class CleanupUnreachableServers extends Command
{
    protected $signature = 'cleanup:unreachable-servers';

    protected $description = 'Cleanup Unreachable Servers (7 days)';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.cleanup_unreachable_servers.description', locale: app()->getLocale()));
    }

    public function handle()
    {
        $this->info(trans('console.cleanup_unreachable_servers.running', locale: app()->getLocale()));
        $servers = Server::where('unreachable_count', '>=', 3)->where('unreachable_notification_sent', true)->where('updated_at', '<', now()->subDays(7))->get();
        if ($servers->count() > 0) {
            foreach ($servers as $server) {
                $this->info(trans('console.cleanup_unreachable_servers.cleanup_server', ['id' => $server->id, 'name' => $server->name], locale: app()->getLocale()));
                $server->update([
                    'ip' => '1.2.3.4',
                ]);
            }
        }
    }
}
