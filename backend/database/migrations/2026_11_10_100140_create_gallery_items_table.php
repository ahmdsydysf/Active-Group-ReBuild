<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_category_id')->constrained('gallery_categories')->cascadeOnDelete();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->string('media', 500)->nullable();
            $table->string('video_url', 500)->nullable();
            $table->string('caption_ar')->nullable();
            $table->string('caption_en')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
