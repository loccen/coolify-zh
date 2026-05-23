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
        ->assertDispatched('error', fn (string $name, array $params): bool => $params['message'] === __('server.toasts.server_with_same_ip_exists_in_team'));

    Livewire::test(ByIp::class, [
        'private_keys' => collect([$this->privateKey]),
        'limit_reached' => false,
    ])
        ->set('ip', '5.6.7.8')
        ->call('submit')
        ->assertDispatched('error', fn (string $name, array $params): bool => $params['message'] === __('server.toasts.server_with_same_ip_in_use_by_another_team'));

    Livewire::test(ByIp::class, [
        'private_keys' => collect(),
        'limit_reached' => false,
    ])
        ->set('ip', '9.9.9.9')
        ->call('submit')
        ->assertDispatched('error', fn (string $name, array $params): bool => $params['message'] === __('server.toasts.select_private_key'));

    config()->set('constants.coolify.self_hosted', false);
    $teamWithLimit = Team::factory()->create(['custom_server_limit' => 0]);
    $userWithLimit = User::factory()->create();
    $userWithLimit->teams()->attach($teamWithLimit, ['role' => 'owner']);
    $limitPrivateKey = PrivateKey::create([
        'name' => 'Limit Key',
        'private_key' => '-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAstJo/SfYh3tquc2BA29a1X3pdPpXazRgtKsb5fHOwQs1rE04
VyJYW6QCToSH4WS1oKt6iI4ma4uivn8rnkZFdw3mpcLp2ofcoeV3YPKX6pN/RiJC
if+g8gCaFywOxy2pjXOLPZeFJSXFqc4UOymbhESUyDnMfk4/RvnubMiv3jINo4Ow
4Tv7tRzAdMlMrx3hEhi142oQuyl1kc4WQOM9cAV0bd+62ga3EYSnsWTnC9AaFtWk
eGC5w/7knHJ5QZ9tKApkG3/29vJXY7WwCRUROEHqkvQhRDP0uqRPBdR48iG87Dwq
ePa6TodkFaVfyHS/OUZzRiTn6MOSyQQFg0QIIwIDAQABAoIBAQCsmGebSJU2lwl4
0oAeZ6E9hG0LagFsSL66QpkHxO9w5bflWRbzCwRLVy6eyE46XzDrJfd7y/ALR1hK
E4ZvGpY7heBDx7BdK1rprAggO6YjVD+42qJsfZ3DVo9jpDOTTWBkVcxkI1Xwd9ej
wHNIcy1WabdM1nSoyC9M+ziEKOOOShXc5Q6e+zEzSBbwjc1fvvXZOH4VXZZ1DllE
xGu0jFS23TLnXATxh8SdfYgnvfZgB5n72P9m/lj3FmkuJq57DLZhBwN3Zd4wom03
K7/J4K2Ssnjdv/HjVgrRgpMv7oMxfclN/Aiq878Ue4Mav6LjnLENyHbyR0WxQjY6
lZ7UMEeJAoGBAOCGepk3rCMFa3a6GagN6lYzAkLxB5y0PsefiDo6w+PeEj1tUvSd
aQkiP7uvUC7a5GNp9yE8W79/O1jJXYJq15kMBpUshzfgdzyzDDCj+qvm6nbTWtP9
rP30h81R+NGdOStgs0OVZSjMWnIoii3Rv3UV4+iQXZd67+wd/kbTWtWVAoGBAMvj
xv4wjt7OwtK/6oAhcNd2V9EUQp6PPpMkUyPicWdsLsoNOcuTpWvEc0AomdIGGjgI
AIor1ggCxjEhbCDaZucOFUghciUup+PjyQyQT+3bjvCWuUmi0Vt51G7RE0jjZjQt
2+W9V4yDcJ5R5ow6veYvT0ZOjVTScDYowTBulgjXAoGBALFxVl7UotQiqmVwempY
ZQSu13C0MIHl6V+2cuEiJEJn9R5a0h7EcIhpatkXmlUNZUY0Lr0ziIb1NJ/ctGwn
qDAqUuF+CXddjJ6KGm4uiiNlIZO7QaMcbqVdph3cVLrEeLQRfltBLGtr5WcnJt1D
UP5lyHK59V2MKSUAJz8uNjFpAoGAL5fR4Y/wKa5V5+AImzQzJPho81MpYd3KG4rF
JYE8O4oTOfLwZMboPEm1JWrUzSPDhwTHK3mkEmajYOCOXvTcRF8TNK0p+ef0JMwN
KDOflMRFj39/bOLmv9Wmct+3ArKiLtftlqkmAJTF+w7fJCiqH0s31A+OChi9PMcy
oV2PBC0CgYAXOm08kFOQA+bPBdLAte8Ga89frh6asH/Z8ucfsz9/zMMG/hhq5nF3
7TItY9Pblc2Fp805J13G96zWLX4YGyLwXXkYs+Ae7QoqjonTw7/mUDARY1Zxs9m/
a1C8EDKapCw5hAhizEFOUQKOygL8Ipn+tmEUkORYdZ8Q8cWFCv9nIw==
-----END RSA PRIVATE KEY-----',
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
        ->assertDispatched('error', fn (string $name, array $params): bool => $params['message'] === __('server.toasts.server_limit_reached'));
});
