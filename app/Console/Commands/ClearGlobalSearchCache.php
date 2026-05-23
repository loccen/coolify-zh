<?php

namespace App\Console\Commands;

use App\Livewire\GlobalSearch;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearGlobalSearchCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'search:clear {--team= : Clear cache for specific team ID} {--all : Clear cache for all teams}';

    /**
     * The console command description.
     */
    protected $description;

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.clear_global_search_cache.description', locale: app()->getLocale()));
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->clearAllTeamsCache();
        }

        if ($teamId = $this->option('team')) {
            return $this->clearTeamCache($teamId);
        }

        // If no options provided, clear cache for current user's team
        if (! auth()->check()) {
            $this->error(trans('console.clear_global_search_cache.error.no_authenticated_user', locale: app()->getLocale()));

            return Command::FAILURE;
        }

        $teamId = auth()->user()->currentTeam()->id;

        return $this->clearTeamCache($teamId);
    }

    private function clearTeamCache(int $teamId): int
    {
        $team = Team::find($teamId);

        if (! $team) {
            $this->error(trans('console.clear_global_search_cache.error.team_not_found', ['team_id' => $teamId], locale: app()->getLocale()));

            return Command::FAILURE;
        }

        GlobalSearch::clearTeamCache($teamId);
        $this->info(trans('console.clear_global_search_cache.info.cleared_team_cache', ['team_name' => $team->name, 'team_id' => $teamId], locale: app()->getLocale()));

        return Command::SUCCESS;
    }

    private function clearAllTeamsCache(): int
    {
        $teams = Team::all();

        if ($teams->isEmpty()) {
            $this->warn(trans('console.clear_global_search_cache.warn.no_teams_found', locale: app()->getLocale()));

            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($teams as $team) {
            GlobalSearch::clearTeamCache($team->id);
            $count++;
        }

        $this->info(trans('console.clear_global_search_cache.info.cleared_all_teams_cache', ['count' => $count], locale: app()->getLocale()));

        return Command::SUCCESS;
    }
}
