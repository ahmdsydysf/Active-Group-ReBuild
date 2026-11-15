<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Article categories. section: advice (الخبرات والنصائح) or blog (المدونة).
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->string('section', 10);
            $table->string('slug', 120)->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['section', 'sort_order']);
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('section', 10);
            $table->foreignId('post_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('team_members')->nullOnDelete();
            $table->string('slug', 160)->unique();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('excerpt_ar', 500)->nullable();
            $table->string('excerpt_en', 500)->nullable();
            $table->longText('body_ar')->nullable();
            $table->longText('body_en')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('seo_description_ar', 170)->nullable();
            $table->string('seo_description_en', 170)->nullable();
            // Draft when false. Active with a future date means scheduled.
            $table->boolean('is_active')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['section', 'is_active', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_categories');
    }
};
