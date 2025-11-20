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
        Schema::table('cards', function (Blueprint $table) {
            $table->json('detected_celebrities')->nullable()->after('analysis_metadata');
            $table->integer('face_count')->default(0)->after('detected_celebrities');
            $table->boolean('has_person')->default(false)->after('face_count');
            $table->json('context_labels')->nullable()->after('has_person');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn([
                'detected_celebrities',
                'face_count',
                'has_person',
                'context_labels'
            ]);
        });
    }
};
