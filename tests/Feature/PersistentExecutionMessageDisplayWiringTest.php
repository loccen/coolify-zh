<?php

it('wires persistent execution message display through the helper in targeted ui surfaces', function () {
    $scheduledTaskExecutions = file_get_contents(base_path('app/Livewire/Project/Shared/ScheduledTask/Executions.php'));
    $scheduledJobs = file_get_contents(base_path('app/Livewire/Settings/ScheduledJobs.php'));
    $scheduledJobsView = file_get_contents(base_path('resources/views/livewire/settings/scheduled-jobs.blade.php'));
    $backupExecutionsView = file_get_contents(base_path('resources/views/livewire/project/database/backup-executions.blade.php'));

    expect($scheduledTaskExecutions)
        ->toContain('use App\Support\PersistentExecutionMessage;')
        ->toContain('PersistentExecutionMessage::forDisplay($this->selectedExecution->message)')
        ->toContain("echo \$execution->message;")
        ->and($scheduledJobs)
        ->toContain('use App\Support\PersistentExecutionMessage;')
        ->toContain("'display_message' => PersistentExecutionMessage::forDisplay(\$execution->message)")
        ->and($scheduledJobsView)
        ->toContain("title=\"{{ \$execution['display_message'] }}\"")
        ->toContain("Str::limit(\$execution['display_message'], 80)")
        ->and($backupExecutionsView)
        ->toContain("PersistentExecutionMessage::forDisplay(data_get(\$execution, 'message'))");
});
