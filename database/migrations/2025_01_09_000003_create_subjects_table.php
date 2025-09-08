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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., "CS101", "MATH101"
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('credits', 4, 2)->default(3.00); // Credit units (e.g., 3.00, 1.50)
            $table->enum('category', ['core', 'major', 'elective', 'general_education', 'nstp', 'physical_education'])->default('core');
            $table->string('department')->nullable();
            $table->integer('year_level')->default(1); // 1st year, 2nd year, etc.
            $table->json('semester_offered')->nullable(); // [1, 2] for 1st and 2nd semester, [3] for summer
            $table->integer('capacity')->default(40); // Maximum number of students
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->boolean('is_laboratory')->default(false);
            $table->integer('laboratory_hours')->default(0);
            $table->integer('lecture_hours')->default(3);
            $table->integer('total_hours')->default(3);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('code');
            $table->index('category');
            $table->index('department');
            $table->index('year_level');
            $table->index('status');
            $table->index('is_laboratory');
            $table->index(['category', 'year_level']);
            $table->index(['department', 'year_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};