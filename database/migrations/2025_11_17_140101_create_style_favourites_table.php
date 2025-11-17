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
        Schema::create('style_favourites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('photo_analysis_id')->constrained('photo_analyses')->onDelete('cascade');
            $table->string('session_id')->nullable(); // For non-authenticated users
            $table->timestamps();
            
            // Ensure a user/session can only favourite a style once
            $table->unique(['user_id', 'photo_analysis_id']);
            $table->unique(['session_id', 'photo_analysis_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('style_favourites');
    }
};
