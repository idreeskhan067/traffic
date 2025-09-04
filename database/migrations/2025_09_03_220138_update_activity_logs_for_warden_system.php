<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Add new columns that don't exist
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('warden_id')->nullable()->after('user_id');
            $table->string('type', 50)->nullable()->after('description');
            $table->text('details')->nullable()->after('action');
            $table->timestamp('timestamp')->nullable()->after('details');
            $table->string('location', 100)->nullable()->after('timestamp');
            $table->json('metadata')->nullable()->after('location');
            
            // Add foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('warden_id')->references('id')->on('users')->onDelete('cascade');
            
            // Add indexes
            $table->index(['warden_id', 'timestamp']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['user_id']);
            $table->dropForeign(['warden_id']);
            
            // Drop indexes
            $table->dropIndex(['warden_id', 'timestamp']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['type']);
            
            // Drop added columns
            $table->dropColumn([
                'user_id',
                'warden_id',
                'type',
                'details',
                'timestamp',
                'location',
                'metadata'
            ]);
        });
    }
};