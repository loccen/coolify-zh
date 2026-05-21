<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledDatabaseBackupExecution extends BaseModel
{
    protected $fillable = [
        'uuid',
        'scheduled_database_backup_id',
        'status',
        'message',
        'size',
        'filename',
        'database_name',
        'finished_at',
        'local_storage_deleted',
        's3_storage_deleted',
        's3_uploaded',
        'is_instance_restore_package',
        'includes_app_key',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            's3_uploaded' => 'boolean',
            'local_storage_deleted' => 'boolean',
            's3_storage_deleted' => 'boolean',
            'is_instance_restore_package' => 'boolean',
            'includes_app_key' => 'boolean',
        ];
    }

    public function scheduledDatabaseBackup(): BelongsTo
    {
        return $this->belongsTo(ScheduledDatabaseBackup::class);
    }
}
