<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('subtitle_ar', 500)->nullable();
            $table->string('subtitle_en', 500)->nullable();
            $table->string('button_ar', 60)->nullable();
            $table->string('button_en', 60)->nullable();
            $table->string('button_url', 500)->nullable();
            $table->string('image', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
