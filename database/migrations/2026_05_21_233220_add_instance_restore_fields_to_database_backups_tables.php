<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scheduled_database_backups', function (Blueprint $table) {
            $table->boolean('include_app_key')
                ->default(false)
                ->after('disable_local_backup');
        });

        Schema::table('scheduled_database_backup_executions', function (Blueprint $table) {
            $table->boolean('is_instance_restore_package')
                ->default(false)
                ->after('s3_uploaded');
            $table->boolean('includes_app_key')
                ->default(false)
                ->after('is_instance_restore_package');
        });
    }

    public function down(): void
    {
        Schema::table('scheduled_database_backup_executions', function (Blueprint $table) {
            $table->dropColumn(['is_instance_restore_package', 'includes_app_key']);
        });

        Schema::table('scheduled_database_backups', function (Blueprint $table) {
            $table->dropColumn('include_app_key');
        });
    }
};
