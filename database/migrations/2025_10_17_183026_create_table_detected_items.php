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
        Schema::create('detected_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained('cards')->onDelete('cascade');
            $table->string('category'); // shirt, pants, dress, etc.
            $table->text('description'); // Full description for search
            $table->string('color')->nullable();
            $table->decimal('confidence', 5, 2); // 0.00 to 100.00
            $table->json('bounding_box')->nullable(); // {x, y, width, height}
            $table->json('raw_data')->nullable(); // Store full Google Vision response
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detected_items');
    }
};
