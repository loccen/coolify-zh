<?php

use App\Livewire\Server\New\ByIp;
use App\Models\PrivateKey;
use App\Models\Server;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function serverByIpTestPrivateKey(): string
{
    return '-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAAAMwAAAAtzc2gtZW
QyNTUxOQAAACBbhpqHhqv6aI67Mj9abM3DVbmcfYhZAhC7ca4d9UCevAAAAJi/QySHv0Mk
hwAAAAtzc2gtZWQyNTUxOQAAACBbhpqHhqv6aI67Mj9abM3DVbmcfYhZAhC7ca4d9UCevA
AAAECBQw4jg1WRT2IGHMncCiZhURCts2s24HoDS0thHnnRKVuGmoeGq/pojrsyP1pszcNV
uZx9iFkCELtxrh31QJ68AAAAEXNhaWxANzZmZjY2ZDJlMmRkAQIDBA==
-----END OPENSSH PRIVATE KEY-----';
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create();
    $this->user->teams()->attach($this->team, ['role' => 'owner']);
    $this->actingAs($this->user);
    session(['currentTeam' => $this->team]);

    $this->privateKey = PrivateKey::create([
        'name' => 'Test Key',
        'private_key' => serverByIpTestPrivateKey(),
        'team_id' => $this->team->id,
    ]);
});

it('dispatches translated toast messages for server by ip validation branches', function () {
    $sameTeamServer = Server::factory()->create([
        'ip' => '1.2.3.4',
        'team_id' => $this->team->id,
        'private_key_id' => $this->privateKey->id,
    ]);

    $otherTeam = Team::factory()->create();
    Server::factory()->create([
        'ip' => '5.6.7.8',
        'team_id' => $otherTeam->id,
        'private_key_id' => $this->privateKey->id,
    ]);

    Livewire::test(ByIp::class, [
        'private_keys' => collect([$this->privateKey]),
        'limit_reached' => false,
    ])
        ->set('ip', $sameTeamServer->ip)
        ->call('submit')
        ->assertDispatched('error', fn (array $event): bool => $event['message'] === __('server.toasts.server_with_same_ip_exists_in_team'));

    Livewire::test(ByIp::class, [
        'private_keys' => collect([$this->privateKey]),
        'limit_reached' => false,
    ])
        ->set('ip', '5.6.7.8')
        ->call('submit')
        ->assertDispatched('error', fn (array $event): bool => $event['message'] === __('server.toasts.server_with_same_ip_in_use_by_another_team'));

    Livewire::test(ByIp::class, [
        'private_keys' => collect(),
        'limit_reached' => false,
    ])
        ->set('ip', '9.9.9.9')
        ->call('submit')
        ->assertDispatched('error', fn (array $event): bool => $event['message'] === __('server.toasts.select_private_key'));

    config()->set('constants.coolify.self_hosted', false);
    $teamWithLimit = Team::factory()->create(['custom_server_limit' => 0]);
    $userWithLimit = User::factory()->create();
    $userWithLimit->teams()->attach($teamWithLimit, ['role' => 'owner']);
    $limitPrivateKey = PrivateKey::create([
        'name' => 'Limit Key',
        'private_key' => serverByIpTestPrivateKey(),
        'team_id' => $teamWithLimit->id,
    ]);

    $this->actingAs($userWithLimit);
    session(['currentTeam' => $teamWithLimit]);

    Livewire::test(ByIp::class, [
        'private_keys' => collect([$limitPrivateKey]),
        'limit_reached' => false,
    ])
        ->set('ip', '10.10.10.10')
        ->call('submit')
        ->assertDispatched('error', fn (array $event): bool => $event['message'] === __('server.toasts.server_limit_reached'));
});
