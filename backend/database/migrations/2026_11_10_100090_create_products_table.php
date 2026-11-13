<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('factory_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug', 160)->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('active_ingredient_ar')->nullable();
            $table->string('active_ingredient_en')->nullable();
            $table->string('formulation_ar')->nullable();
            $table->string('formulation_en')->nullable();
            $table->string('summary_ar', 500)->nullable();
            $table->string('summary_en', 500)->nullable();
            $table->longText('body_ar')->nullable();
            $table->longText('body_en')->nullable();
            // Pack sizes as typed, e.g. "250 جم · 500 جم · 1 كجم"
            $table->string('packs_ar')->nullable();
            $table->string('packs_en')->nullable();
            $table->string('registration_no', 60)->nullable();
            // Dose table rows: [{crop, pest, rate, interval, phi}]
            $table->json('doses_ar')->nullable();
            $table->json('doses_en')->nullable();
            // [{q, a}]
            $table->json('faqs_ar')->nullable();
            $table->json('faqs_en')->nullable();
            // One precaution per line
            $table->text('safety_ar')->nullable();
            $table->text('safety_en')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('leaflet', 500)->nullable();
            $table->string('seo_title_ar', 70)->nullable();
            $table->string('seo_title_en', 70)->nullable();
            $table->string('seo_description_ar', 170)->nullable();
            $table->string('seo_description_en', 170)->nullable();
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['product_type_id', 'is_active']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
