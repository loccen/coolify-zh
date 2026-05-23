<?php

use App\Console\Commands\ClearGlobalSearchCache;
use Illuminate\Support\Facades\App;

it('wires clear global search cache command strings through console translations', function () {
    $command = file_get_contents(app_path('Console/Commands/ClearGlobalSearchCache.php'));

    expect($command)
        ->toContain("trans('console.clear_global_search_cache.description'")
        ->toContain("trans('console.clear_global_search_cache.error.no_authenticated_user'")
        ->toContain("trans('console.clear_global_search_cache.error.team_not_found'")
        ->toContain("trans('console.clear_global_search_cache.warn.no_teams_found'")
        ->toContain("trans('console.clear_global_search_cache.info.cleared_team_cache'")
        ->toContain("trans('console.clear_global_search_cache.info.cleared_all_teams_cache'");
});

it('resolves clear global search cache translations in en and zh_CN', function () {
    App::setLocale('en');

    expect(trans('console.clear_global_search_cache.description'))->toBe('Clear the global search cache')
        ->and(trans('console.clear_global_search_cache.error.no_authenticated_user'))->toBe('No authenticated user found. Use --team=TEAM_ID or --all.')
        ->and(trans('console.clear_global_search_cache.error.team_not_found', ['team_id' => 7]))->toBe('Team with ID 7 not found.')
        ->and(trans('console.clear_global_search_cache.warn.no_teams_found'))->toBe('No teams found.')
        ->and(trans('console.clear_global_search_cache.info.cleared_team_cache', ['team_name' => 'Alpha', 'team_id' => 7]))->toBe('Cleared global search cache for team Alpha (ID: 7).')
        ->and(trans('console.clear_global_search_cache.info.cleared_all_teams_cache', ['count' => 3]))->toBe('Cleared global search cache for 3 team(s).')
        ->and((new ClearGlobalSearchCache)->getDescription())->toBe('Clear the global search cache');

    App::setLocale('zh_CN');

    expect(trans('console.clear_global_search_cache.description'))->toBe('清除全局搜索缓存')
        ->and(trans('console.clear_global_search_cache.error.no_authenticated_user'))->toBe('未找到已登录用户。请使用 --team=TEAM_ID 或 --all。')
        ->and(trans('console.clear_global_search_cache.error.team_not_found', ['team_id' => 7]))->toBe('未找到 ID 为 7 的团队。')
        ->and(trans('console.clear_global_search_cache.warn.no_teams_found'))->toBe('未找到团队。')
        ->and(trans('console.clear_global_search_cache.info.cleared_team_cache', ['team_name' => 'Alpha', 'team_id' => 7]))->toBe('已清除团队 Alpha（ID：7）的全局搜索缓存。')
        ->and(trans('console.clear_global_search_cache.info.cleared_all_teams_cache', ['count' => 3]))->toBe('已清除 3 个团队的全局搜索缓存。')
        ->and((new ClearGlobalSearchCache)->getDescription())->toBe('清除全局搜索缓存');
});
