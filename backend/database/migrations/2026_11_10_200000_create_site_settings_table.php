<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            foreach (['ar', 'en'] as $l) {
                $table->string("site_name_{$l}")->nullable();
                $table->string("slogan_{$l}")->nullable();
                $table->longText("overview_{$l}")->nullable();
                $table->longText("mission_{$l}")->nullable();
                $table->longText("vision_{$l}")->nullable();
                $table->string("address_{$l}", 500)->nullable();
                $table->string("address2_{$l}", 500)->nullable();
                $table->text("seo_description_{$l}")->nullable();
            }
            $table->string('logo', 500)->nullable();
            $table->string('about_image', 500)->nullable();
            $table->string('video_url', 500)->nullable();
            $table->string('phones')->nullable();
            $table->string('phones2')->nullable();
            $table->string('email')->nullable();
            $table->string('email2')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('fax')->nullable();
            $table->text('map_url')->nullable();
            $table->text('map_url2')->nullable();
            $table->string('facebook', 500)->nullable();
            $table->string('twitter', 500)->nullable();
            $table->string('linkedin', 500)->nullable();
            $table->string('instagram', 500)->nullable();
            $table->string('youtube', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
