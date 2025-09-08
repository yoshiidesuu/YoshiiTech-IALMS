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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "First Semester", "Second Semester", "Summer Term"
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('enrollment_start');
            $table->date('enrollment_end');
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->text('description')->nullable();
            $table->integer('term_number')->default(1); // 1 for first semester, 2 for second, etc.
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('academic_year_id');
            $table->index('status');
            $table->index('is_current');
            $table->index(['start_date', 'end_date']);
            $table->index(['enrollment_start', 'enrollment_end']);
            $table->index('term_number');
            
            // Unique constraint to prevent duplicate semester names within the same academic year
            $table->unique(['academic_year_id', 'name'], 'unique_semester_per_academic_year');
            
            // Note: Unique constraint for current semester will be handled at application level
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};