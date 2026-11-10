<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('position_ar')->nullable();
            $table->string('position_en')->nullable();
            $table->string('photo', 500)->nullable();
            $table->string('facebook', 500)->nullable();
            $table->string('twitter', 500)->nullable();
            $table->string('instagram', 500)->nullable();
            $table->string('linkedin', 500)->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
