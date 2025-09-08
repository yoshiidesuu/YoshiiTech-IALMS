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
        Schema::create('grade_encoding_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->enum('status', ['draft', 'active', 'closed', 'extended', 'archived'])->default('draft');
            $table->text('description')->nullable();
            $table->enum('grade_type', ['midterm', 'final', 'completion', 'removal', 'special'])->default('final');
            $table->boolean('is_extendable')->default(true);
            $table->datetime('extension_deadline')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('academic_year_id');
            $table->index('semester_id');
            $table->index('status');
            $table->index('grade_type');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('extension_deadline');
            $table->index('created_by');
            $table->index(['academic_year_id', 'semester_id']);
            $table->index(['status', 'start_date', 'end_date']);
            $table->index(['grade_type', 'status']);
            
            // Unique constraint to prevent overlapping periods of same type
            $table->unique(['academic_year_id', 'semester_id', 'grade_type'], 'unique_period_per_semester_grade_type');
            
            // Note: Date validation constraints are handled at application level
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_encoding_periods');
    }
};