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
        Schema::create('subject_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('prerequisite_id')->constrained('subjects')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('subject_id');
            $table->index('prerequisite_id');
            
            // Unique constraint to prevent duplicate prerequisites
            $table->unique(['subject_id', 'prerequisite_id'], 'unique_subject_prerequisite');
            
            // Note: Self-prerequisite prevention is handled at application level
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_prerequisites');
    }
};