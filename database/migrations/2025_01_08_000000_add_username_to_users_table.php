<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
        });
        
        // Generate usernames for existing users based on email
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $baseUsername = strtolower(explode('@', $user->email)[0]);
            $username = $baseUsername;
            $counter = 1;
            
            // Ensure username is unique
            while (DB::table('users')->where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            
            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $username]);
        }
        
        // Make username required after populating existing records
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};