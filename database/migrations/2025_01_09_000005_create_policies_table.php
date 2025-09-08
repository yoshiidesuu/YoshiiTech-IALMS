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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->enum('category', ['academic', 'administrative', 'student_affairs', 'faculty', 'financial', 'disciplinary', 'general'])->default('general');
            $table->string('version')->default('1.0');
            $table->enum('status', ['draft', 'review', 'approved', 'published', 'archived', 'expired'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('parent_policy_id')->nullable()->constrained('policies')->onDelete('cascade');
            $table->text('summary')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('category');
            $table->index('status');
            $table->index('version');
            $table->index('published_at');
            $table->index('effective_date');
            $table->index('expiry_date');
            $table->index('created_by');
            $table->index('approved_by');
            $table->index('parent_policy_id');
            $table->index(['category', 'status']);
            $table->index(['status', 'published_at']);
            $table->index(['effective_date', 'expiry_date']);
            
            // Full-text search index for title and content
            $table->fullText(['title', 'content', 'summary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};