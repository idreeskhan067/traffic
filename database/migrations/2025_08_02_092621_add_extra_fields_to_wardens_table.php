<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('wardens', function (Blueprint $table) {
        if (!Schema::hasColumn('wardens', 'designation')) {
            $table->string('designation')->nullable();
        }
        if (!Schema::hasColumn('wardens', 'team_id')) {
            $table->unsignedBigInteger('team_id')->nullable();
        }
        if (!Schema::hasColumn('wardens', 'longitude')) {
            $table->decimal('longitude', 10, 7)->nullable();
        }
        if (!Schema::hasColumn('wardens', 'last_logged_in_at')) {
            $table->timestamp('last_logged_in_at')->nullable();
        }
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wardens', function (Blueprint $table) {
            //
        });
    }
};
