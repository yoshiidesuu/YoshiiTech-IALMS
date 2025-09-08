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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->string('display_name'); // Added from system_configurations
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json, array, file
            $table->string('group')->nullable()->index(); // Group configurations (e.g., 'app', 'mail', 'database')
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Whether this config can be accessed publicly
            $table->boolean('is_editable')->default(true); // Added from system_configurations
            $table->boolean('is_encrypted')->default(false); // Whether the value is encrypted
            $table->json('validation_rules')->nullable(); // JSON validation rules
            $table->json('options')->nullable(); // Added from system_configurations - JSON options for select/radio inputs
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['group', 'key']);
            $table->index(['group', 'sort_order']); // Added from system_configurations
            $table->index(['is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
