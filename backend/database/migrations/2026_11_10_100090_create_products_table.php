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
            $table->string('slug')->unique();
            $table->foreignId('product_type_id')->constrained('product_types')->cascadeOnDelete();
            $table->foreignId('factory_id')->nullable()->constrained('factories')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->longText('description_ar')->nullable();
            $table->longText('description_en')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('brochure_ar', 500)->nullable();
            $table->string('brochure_en', 500)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
