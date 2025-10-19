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
        Schema::create('photo_analyses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('image_path');
            $table->string('image_url');
            $table->integer('file_size')->nullable();
            $table->string('dimensions')->nullable();
            $table->json('analysis_metadata')->nullable();
            $table->string('status')->default('processing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_analyses');
    }
};
