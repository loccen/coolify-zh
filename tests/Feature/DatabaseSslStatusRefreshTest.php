<?php

use App\Livewire\Project\Database\Dragonfly\General as DragonflyGeneral;
use App\Livewire\Project\Database\Keydb\General as KeydbGeneral;
use App\Livewire\Project\Database\Mariadb\General as MariadbGeneral;
use App\Livewire\Project\Database\Mongodb\General as MongodbGeneral;
use App\Livewire\Project\Database\Mysql\General as MysqlGeneral;
use App\Livewire\Project\Database\Postgresql\General as PostgresqlGeneral;
use App\Livewire\Project\Database\Redis\General as RedisGeneral;
use App\Models\Environment;
use App\Models\Project;
use App\Models\Server;
use App\Models\StandaloneDocker;
use App\Models\StandaloneMysql;
use App\Models\StandalonePostgresql;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->team = Team::factory()->create();
    $this->user = User::factory()->create();
    $this->team->members()->attach($this->user->id, ['role' => 'owner']);

    $this->actingAs($this->user);
    session(['currentTeam' => $this->team]);
});

dataset('ssl-aware-database-general-components', [
    MysqlGeneral::class,
    MariadbGeneral::class,
    MongodbGeneral::class,
    RedisGeneral::class,
    PostgresqlGeneral::class,
    KeydbGeneral::class,
    DragonflyGeneral::class,
]);

it('maps database status broadcasts to refresh for ssl-aware database general components', function (string $componentClass) {
    $component = app($componentClass);
    $listeners = $component->getListeners();

    expect($listeners["echo-private:user.{$this->user->id},DatabaseStatusChanged"])->toBe('refresh')
        ->and($listeners["echo-private:team.{$this->team->id},ServiceChecked"])->toBe('refresh');
})->with('ssl-aware-database-general-components');

it('reloads the mysql database model when refreshing so ssl controls follow the latest status', function () {
    $server = Server::factory()->create(['team_id' => $this->team->id]);
    $destination = StandaloneDocker::where('server_id', $server->id)->first();
    $project = Project::factory()->create(['team_id' => $this->team->id]);
    $environment = Environment::factory()->create(['project_id' => $project->id]);

    $database = StandaloneMysql::create([
        'name' => 'test-mysql',
        'image' => 'mysql:8',
        'mysql_root_password' => 'password',
        'mysql_user' => 'coolify',
        'mysql_password' => 'password',
        'mysql_database' => 'coolify',
        'status' => 'exited:unhealthy',
        'enable_ssl' => true,
        'is_log_drain_enabled' => false,
        'environment_id' => $environment->id,
        'destination_id' => $destination->id,
        'destination_type' => $destination->getMorphClass(),
    ]);

    $helperText = __('Database should be stopped to change this settings.');

    $component = Livewire::test(MysqlGeneral::class, ['database' => $database])
        ->assertDontSee($helperText);

    $database->fill(['status' => 'running:healthy'])->save();

    $component->call('refresh')
        ->assertSee($helperText);
});

it('keeps postgresql validation errors in english when locale is en', function () {
    App::setLocale('en');

    $server = Server::factory()->create(['team_id' => $this->team->id]);
    $destination = StandaloneDocker::where('server_id', $server->id)->first();
    $project = Project::factory()->create(['team_id' => $this->team->id]);
    $environment = Environment::factory()->create(['project_id' => $project->id]);

    $database = StandalonePostgresql::create([
        'name' => 'test-postgres',
        'image' => 'postgres:15-alpine',
        'postgres_user' => 'postgres',
        'postgres_password' => 'password',
        'postgres_db' => 'testdb',
        'status' => 'exited:unhealthy',
        'enable_ssl' => false,
        'is_log_drain_enabled' => false,
        'environment_id' => $environment->id,
        'destination_id' => $destination->id,
        'destination_type' => $destination->getMorphClass(),
    ]);

    $component = Livewire::test(PostgresqlGeneral::class, ['database' => $database]);
    $instance = $component->instance();

    $validator = validator(
        ['name' => ''],
        ['name' => (fn () => $this->rules()['name'])->call($instance)],
        ['name.required' => (fn () => $this->messages()['name.required'])->call($instance)],
        ['name' => (fn () => $this->validationAttributes()['name'])->call($instance)],
    );

    expect($validator->errors()->first('name'))->toBe('The Name field is required.');
});

it('uses plain html title attributes in ssl mode option lists', function () {
    $mysqlView = file_get_contents(resource_path('views/livewire/project/database/mysql/general.blade.php'));
    $mongodbView = file_get_contents(resource_path('views/livewire/project/database/mongodb/general.blade.php'));
    $postgresqlView = file_get_contents(resource_path('views/livewire/project/database/postgresql/general.blade.php'));

    expect($mysqlView)
        ->toContain('title="{{ __(\'Prefer secure connections\') }}"')
        ->not->toContain(':title="__(\'Prefer secure connections\')"')
        ->and($mongodbView)
        ->toContain('title="{{ __(\'Allow insecure connections\') }}"')
        ->not->toContain(':title="__(\'Allow insecure connections\')"')
        ->and($postgresqlView)
        ->toContain('title="{{ __(\'Verify full certificate\') }}"')
        ->not->toContain(':title="__(\'Verify full certificate\')"');
});
