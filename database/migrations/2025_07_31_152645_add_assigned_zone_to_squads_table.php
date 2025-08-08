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
    Schema::table('squads', function (Blueprint $table) {
        $table->string('assigned_zone')->nullable()->after('status');
    });
}


    /**
     * Reverse the migrations.
     */
public function down()
{
    Schema::table('squads', function (Blueprint $table) {
        $table->dropColumn('assigned_zone');
    });
}

};
