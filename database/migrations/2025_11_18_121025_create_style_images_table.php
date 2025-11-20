<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('style_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('path');
            $table->string('url');
            $table->string('filename')->nullable();
            $table->string('original_filename')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('dimensions')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        // Backfill existing images from cards metadata/columns
        $photoAnalyses = DB::table('cards')->select('id', 'image_path', 'image_url', 'file_size', 'dimensions', 'analysis_metadata')->get();

        foreach ($photoAnalyses as $analysis) {
            $metadata = $analysis->analysis_metadata ? json_decode($analysis->analysis_metadata, true) : [];
            $images = isset($metadata['images']) && is_array($metadata['images']) ? $metadata['images'] : null;

            if ($images && count($images) > 0) {
                foreach ($images as $index => $image) {
                    DB::table('style_images')->insert([
                        'card_id' => $analysis->id,
                        'path' => $image['path'] ?? $analysis->image_path,
                        'url' => $image['url'] ?? $analysis->image_url,
                        'filename' => $image['filename'] ?? basename($image['path'] ?? $analysis->image_path ?? ''),
                        'original_filename' => $image['original_filename'] ?? ($metadata['original_filename'] ?? null),
                        'file_size' => $image['file_size'] ?? $analysis->file_size,
                        'dimensions' => $image['dimensions'] ?? $analysis->dimensions,
                        'position' => $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                DB::table('style_images')->insert([
                    'card_id' => $analysis->id,
                    'path' => $analysis->image_path,
                    'url' => $analysis->image_url,
                    'filename' => basename($analysis->image_path ?? ''),
                    'original_filename' => $metadata['original_filename'] ?? null,
                    'file_size' => $analysis->file_size,
                    'dimensions' => $analysis->dimensions,
                    'position' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('style_images');
    }
};
