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
        Schema::create('lecture_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('lecturer_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('room');

            $table->enum('day_of_week', [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
            ]);

            // Use time columns so only "HH:MM:SS" is stored
            $table->time('start_time');
            $table->time('end_time');

            // Store the semester start/end dates
            $table->date('start_date');
            $table->date('end_date');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_schedules');
    }
};
