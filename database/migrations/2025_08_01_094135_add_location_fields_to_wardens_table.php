<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wardens', function (Blueprint $table) {
            if (!Schema::hasColumn('wardens', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('wardens', 'email')) {
                $table->string('email')->unique()->nullable();
            }
            if (!Schema::hasColumn('wardens', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('wardens', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('wardens', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('wardens', 'last_logged_in_at')) {
                $table->timestamp('last_logged_in_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('wardens', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'email', 'phone',
                'latitude', 'longitude', 'last_logged_in_at'
            ]);
        });
    }
};
