<div>
    <x-slot:title>
        {{ __('settings.scheduled_jobs_page.page_title') }} | Coolify
    </x-slot>
    <x-settings.navbar />
    <div x-data="{ activeTab: window.location.hash ? window.location.hash.substring(1) : 'executions' }"
        class="flex flex-col gap-8">
        <div>
            <div class="flex items-center gap-2">
                <h2>{{ __('settings.scheduled_jobs_page.heading') }}</h2>
                <x-forms.button wire:click="refresh">{{ __('settings.scheduled_jobs_page.refresh') }}</x-forms.button>
            </div>
            <div class="pb-4">{{ __('settings.scheduled_jobs_page.subtitle') }}</div>
        </div>

        <div class="flex flex-row gap-4">
            <div @class([
                    'box-without-bg cursor-pointer dark:bg-coolgray-100 dark:text-white w-full text-center items-center justify-center',
                ])
                :class="activeTab === 'executions' && 'dark:bg-coollabs bg-coollabs text-white'"
                @click="activeTab = 'executions'; window.location.hash = 'executions'">
                {{ __('settings.scheduled_jobs_page.tabs.executions') }} ({{ $executions->count() }})
            </div>
            <div @class([
                    'box-without-bg cursor-pointer dark:bg-coolgray-100 dark:text-white w-full text-center items-center justify-center',
                ])
                :class="activeTab === 'scheduler-runs' && 'dark:bg-coollabs bg-coollabs text-white'"
                @click="activeTab = 'scheduler-runs'; window.location.hash = 'scheduler-runs'">
                {{ __('settings.scheduled_jobs_page.tabs.scheduler_runs') }} ({{ $managerRuns->count() }})
            </div>
            <div @class([
                    'box-without-bg cursor-pointer dark:bg-coolgray-100 dark:text-white w-full text-center items-center justify-center',
                ])
                :class="activeTab === 'skipped-jobs' && 'dark:bg-coollabs bg-coollabs text-white'"
                @click="activeTab = 'skipped-jobs'; window.location.hash = 'skipped-jobs'">
                {{ __('settings.scheduled_jobs_page.tabs.skipped_jobs') }} ({{ $skipTotalCount }})
            </div>
        </div>

        <div x-show="activeTab === 'executions'" x-cloak>
            <div class="flex gap-4 flex-wrap mb-4">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">{{ __('settings.scheduled_jobs_page.filters.type') }}</label>
                    <select wire:model.live="filterType"
                        class="w-40 border bg-white dark:bg-coolgray-100 border-gray-300 dark:border-coolgray-400 rounded-md text-sm">
                        <option value="all">{{ __('settings.scheduled_jobs_page.filters.all_types') }}</option>
                        <option value="backup">{{ __('settings.scheduled_jobs_page.types.backup') }}</option>
                        <option value="task">{{ __('settings.scheduled_jobs_page.types.task') }}</option>
                        <option value="cleanup">{{ __('settings.scheduled_jobs_page.types.docker_cleanup') }}</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">{{ __('settings.scheduled_jobs_page.filters.time_range') }}</label>
                    <select wire:model.live="filterDate"
                        class="w-40 border bg-white dark:bg-coolgray-100 border-gray-300 dark:border-coolgray-400 rounded-md text-sm">
                        <option value="last_24h">{{ __('settings.scheduled_jobs_page.filters.last_24_hours') }}</option>
                        <option value="last_7d">{{ __('settings.scheduled_jobs_page.filters.last_7_days') }}</option>
                        <option value="last_30d">{{ __('settings.scheduled_jobs_page.filters.last_30_days') }}</option>
                        <option value="all">{{ __('settings.scheduled_jobs_page.filters.all_time') }}</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-coolgray-200">
                        <tr>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.filters.type') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.resource') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.server') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.started') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.duration') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.message') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($executions as $execution)
                            <tr wire:key="exec-{{ $execution['type'] }}-{{ $execution['id'] }}"
                                class="border-b border-gray-200 dark:border-coolgray-400 hover:bg-gray-50 dark:hover:bg-coolgray-200">
                                <td class="px-4 py-3">
                                    @php
                                        $typeLabel = match($execution['type']) {
                                            'backup' => __('settings.scheduled_jobs_page.types.backup'),
                                            'task' => __('settings.scheduled_jobs_page.types.task'),
                                            'cleanup' => __('settings.scheduled_jobs_page.types.cleanup'),
                                            default => ucfirst($execution['type']),
                                        };
                                        $typeBg = match($execution['type']) {
                                            'backup' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                            'task' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                            'cleanup' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-md text-xs font-medium {{ $typeBg }}">
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $execution['resource_name'] }}
                                    @if($execution['resource_type'])
                                        <span class="text-xs text-gray-500">({{ $execution['resource_type'] }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $execution['server_name'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $execution['created_at']->diffForHumans() }}
                                    <span class="block text-xs text-gray-500">{{ $execution['created_at']->format('M d H:i') }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($execution['finished_at'] && $execution['created_at'])
                                        {{ \Carbon\Carbon::parse($execution['created_at'])->diffInSeconds(\Carbon\Carbon::parse($execution['finished_at'])) }}s
                                    @elseif($execution['status'] === 'running')
                                        <x-loading class="w-4 h-4" />
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $execution['display_message'] }}">
                                    {{ \Illuminate\Support\Str::limit($execution['display_message'], 80) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    {{ __('settings.scheduled_jobs_page.empty_failures') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="activeTab === 'scheduler-runs'" x-cloak>
            <div class="pb-4 text-sm text-gray-500">{{ __('settings.scheduled_jobs_page.scheduler_runs_subtitle') }}</div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-coolgray-200">
                        <tr>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.time') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.event') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.duration') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.dispatched') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.skipped') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($managerRuns as $run)
                            <tr wire:key="run-{{ $loop->index }}"
                                class="border-b border-gray-200 dark:border-coolgray-400">
                                <td class="px-4 py-2 whitespace-nowrap text-xs">{{ $run['timestamp'] }}</td>
                                <td class="px-4 py-2">{{ $run['message'] }}</td>
                                <td class="px-4 py-2">
                                    @if($run['duration_ms'] !== null)
                                        {{ $run['duration_ms'] }}ms
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $run['dispatched'] ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    @if(($run['skipped'] ?? 0) > 0)
                                        <span class="text-warning">{{ $run['skipped'] }}</span>
                                    @else
                                        {{ $run['skipped'] ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                    {{ __('settings.scheduled_jobs_page.empty_scheduler_runs') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="activeTab === 'skipped-jobs'" x-cloak>
            <div class="pb-4 text-sm text-gray-500">{{ __('settings.scheduled_jobs_page.skipped_jobs_subtitle') }}</div>
            @if($skipTotalCount > $skipDefaultTake)
                <div class="flex items-center gap-2 mb-4">
                    <x-forms.button disabled="{{ !$showSkipPrev }}" wire:click="skipPreviousPage">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </x-forms.button>
                    <span class="text-sm">
                        {{ __('settings.scheduled_jobs_page.page_of', ['current' => $skipCurrentPage, 'total' => ceil($skipTotalCount / $skipDefaultTake)]) }}
                    </span>
                    <x-forms.button disabled="{{ !$showSkipNext }}" wire:click="skipNextPage">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5l7 7-7 7" />
                        </svg>
                    </x-forms.button>
                </div>
            @endif
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-coolgray-200">
                        <tr>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.time') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.filters.type') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.resource') }}</th>
                            <th class="px-4 py-3">{{ __('settings.scheduled_jobs_page.table.reason') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($skipLogs as $skip)
                            <tr wire:key="skip-{{ $loop->index }}"
                                class="border-b border-gray-200 dark:border-coolgray-400">
                                <td class="px-4 py-2 whitespace-nowrap text-xs">{{ $skip['timestamp'] }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $skipTypeBg = match($skip['type']) {
                                            'backup' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                            'task' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                            'docker_cleanup' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-md text-xs font-medium {{ $skipTypeBg }}">
                                        {{ __('settings.scheduled_jobs_page.types.' . $skip['type']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    @if($skip['link'] ?? null)
                                        <a href="{{ $skip['link'] }}" class="text-white underline hover:no-underline">
                                            {{ $skip['resource_name'] }}
                                        </a>
                                    @elseif($skip['resource_name'] ?? null)
                                        {{ $skip['resource_name'] }}
                                    @else
                                        <span class="text-gray-500">{{ $skip['context']['task_name'] ?? $skip['context']['server_name'] ?? __('settings.scheduled_jobs_page.resources.deleted') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @php
                                        $reasonLabel = match($skip['reason']) {
                                            'server_not_functional' => __('settings.scheduled_jobs_page.reasons.server_not_functional'),
                                            'subscription_unpaid' => __('settings.scheduled_jobs_page.reasons.subscription_unpaid'),
                                            'database_deleted' => __('settings.scheduled_jobs_page.reasons.database_deleted'),
                                            'server_deleted' => __('settings.scheduled_jobs_page.reasons.server_deleted'),
                                            'resource_deleted' => __('settings.scheduled_jobs_page.reasons.resource_deleted'),
                                            'application_not_running' => __('settings.scheduled_jobs_page.reasons.application_not_running'),
                                            'service_not_running' => __('settings.scheduled_jobs_page.reasons.service_not_running'),
                                            default => ucfirst(str_replace('_', ' ', $skip['reason'])),
                                        };
                                        $reasonBg = match($skip['reason']) {
                                            'server_not_functional', 'database_deleted', 'server_deleted', 'resource_deleted' => 'text-red-600 dark:text-red-400',
                                            'subscription_unpaid' => 'text-warning',
                                            'application_not_running', 'service_not_running' => 'text-orange-600 dark:text-orange-400',
                                            default => '',
                                        };
                                    @endphp
                                    <span class="{{ $reasonBg }}">{{ $reasonLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                    {{ __('settings.scheduled_jobs_page.empty_skipped_jobs') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
