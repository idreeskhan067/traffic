<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up(): void
{
    Schema::table('activity_logs', function (Blueprint $table) {
        if (!Schema::hasColumn('activity_logs', 'action')) {
            $table->string('action')->nullable()->after('id');
        }
        if (!Schema::hasColumn('activity_logs', 'performed_by')) {
            $table->string('performed_by')->nullable()->after('action');
        }
        if (!Schema::hasColumn('activity_logs', 'target')) {
            $table->string('target')->nullable()->after('performed_by');
        }
        if (!Schema::hasColumn('activity_logs', 'description')) {
            $table->text('description')->nullable()->after('target');
        }
    });
}


    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['action', 'performed_by', 'target', 'description']);
        });
    }
};
