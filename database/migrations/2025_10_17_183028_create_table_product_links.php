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
        Schema::create('product_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detected_item_id')->constrained('detected_items')->onDelete('cascade');
            $table->string('platform'); // Amazon, Google Shopping
            $table->string('title');
            $table->text('url'); // Affiliate link
            $table->string('price')->nullable();
            $table->string('image_url')->nullable();
            $table->string('asin')->nullable(); // Amazon specific
            $table->string('search_query');
            $table->json('raw_data')->nullable(); // Store full API response
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_links');
    }
};
