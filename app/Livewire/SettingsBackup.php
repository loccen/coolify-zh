<?php

namespace App\Livewire;

use App\Models\InstanceSettings;
use App\Models\S3Storage;
use App\Models\ScheduledDatabaseBackup;
use App\Models\Server;
use App\Models\StandaloneDocker;
use App\Models\StandalonePostgresql;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SettingsBackup extends Component
{
    public InstanceSettings $settings;

    public Server $server;

    public ?StandalonePostgresql $database = null;

    public ?ScheduledDatabaseBackup $backup = null;

    #[Locked]
    public $s3s;

    #[Locked]
    public $executions = [];

    #[Validate(['required'])]
    public string $uuid;

    #[Validate(['required'])]
    public string $name;

    #[Validate(['nullable'])]
    public ?string $description = null;

    #[Validate(['required'])]
    public string $postgres_user;

    #[Validate(['required'])]
    public string $postgres_password;

    public function mount()
    {
        if (! isInstanceAdmin()) {
            return redirect()->route('dashboard');
        }
        $settings = instanceSettings();
        $this->server = Server::findOrFail(0);
        $this->database = StandalonePostgresql::find(0) ?? StandalonePostgresql::whereName('coolify-db')->first();
        $s3s = S3Storage::whereTeamId(0)->get() ?? [];
        if ($this->database) {
            $this->uuid = $this->database->uuid;
            $this->name = $this->database->name;
            $this->description = $this->database->description;
            $this->postgres_user = $this->database->postgres_user;
            $this->postgres_password = $this->database->postgres_password;

            if ($this->database->status !== 'running') {
                $this->database->status = 'running';
                $this->database->save();
            }
            $this->backup = $this->database->scheduledBackups->first();
            if ($this->backup && ! $this->server->isFunctional()) {
                $this->backup->enabled = false;
                $this->backup->save();
            }
            $this->executions = $this->backup?->executions ?? [];
        }
        $this->settings = $settings;
        $this->s3s = $s3s;
    }

    public function addCoolifyDatabase()
    {
        try {
            $server = Server::findOrFail(0);
            $out = instant_remote_process(['docker inspect coolify-db'], $server);
            $envs = format_docker_envs_to_json($out);
            $this->database = $this->syncCoolifyDatabaseFromEnv($envs);
            $this->backup = $this->ensureCoolifyDatabaseBackup($this->database, currentTeam()->id);
            $this->s3s = S3Storage::whereTeamId(0)->get();

            $this->uuid = $this->database->uuid;
            $this->name = $this->database->name;
            $this->description = $this->database->description;
            $this->postgres_user = $this->database->postgres_user;
            $this->postgres_password = $this->database->postgres_password;
            $this->executions = $this->backup->executions;

        } catch (\Exception $e) {
            return handleError($e, $this);
        }
    }

    public function submit()
    {
        $this->validate();

        $this->database->update([
            'name' => $this->name,
            'description' => $this->description,
            'postgres_user' => $this->postgres_user,
            'postgres_password' => $this->postgres_password,
        ]);
        $this->dispatch('success', __('settings.backup_page.updated'));
    }

    private function syncCoolifyDatabaseFromEnv(Collection|array $envs): StandalonePostgresql
    {
        $envs = $envs instanceof Collection ? $envs->toArray() : $envs;
        $database = StandalonePostgresql::find(0) ?? StandalonePostgresql::whereName('coolify-db')->first() ?? new StandalonePostgresql;
        $payload = [
            'name' => 'coolify-db',
            'description' => 'Coolify database',
            'postgres_user' => $envs['POSTGRES_USER'],
            'postgres_password' => $envs['POSTGRES_PASSWORD'],
            'postgres_db' => $envs['POSTGRES_DB'],
            'status' => 'running',
            'destination_type' => StandaloneDocker::class,
            'destination_id' => 0,
        ];

        if (! $database->exists && ! StandalonePostgresql::whereKey(0)->exists()) {
            $payload['id'] = 0;
        }

        $database->forceFill($payload);
        $database->save();

        return $database->fresh();
    }

    private function ensureCoolifyDatabaseBackup(StandalonePostgresql $database, int $teamId): ScheduledDatabaseBackup
    {
        $backup = ScheduledDatabaseBackup::query()
            ->where('database_id', $database->id)
            ->where('database_type', StandalonePostgresql::class)
            ->first();

        if ($backup) {
            return $backup->fresh();
        }

        $payload = [
            'enabled' => true,
            'save_s3' => false,
            'frequency' => '0 0 * * *',
            'include_app_key' => false,
            'database_id' => $database->id,
            'database_type' => StandalonePostgresql::class,
            'team_id' => $teamId,
        ];

        if (! ScheduledDatabaseBackup::whereKey(0)->exists()) {
            $payload['id'] = 0;
        }

        return ScheduledDatabaseBackup::create($payload)->fresh();
    }
}
