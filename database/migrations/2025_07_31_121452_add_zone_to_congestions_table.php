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
    Schema::table('congestions', function (Blueprint $table) {
        $table->string('zone')->nullable();
    });
}

public function down()
{
    Schema::table('congestions', function (Blueprint $table) {
        $table->dropColumn('zone');
    });
}

};
