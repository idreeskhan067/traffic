<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');                      // Incident title
            $table->text('description');                  // Detailed description
            $table->string('location');                   // Location (text or address)
            $table->enum('status', ['Pending', 'Resolved', 'In Progress'])->default('Pending'); // Incident status
            $table->string('reported_by');                // Name or ID of person reporting
            $table->timestamp('reported_at')->nullable(); // Date/time reported
            $table->string('image')->nullable();          // Optional image
            $table->timestamps();                         // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
