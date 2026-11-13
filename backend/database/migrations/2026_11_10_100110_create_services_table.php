<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Technical tips and consultations ("الخبرات والنصائح"). The home page shows the latest five.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_type_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 160)->unique();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('excerpt_ar', 500)->nullable();
            $table->string('excerpt_en', 500)->nullable();
            $table->longText('body_ar')->nullable();
            $table->longText('body_en')->nullable();
            $table->string('image', 500)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
