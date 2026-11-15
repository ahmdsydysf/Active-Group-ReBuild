<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Contact form submissions.
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('name', 120);
            $table->string('phone', 20);
            $table->string('email', 120)->nullable();
            $table->string('governorate', 60)->nullable();
            $table->text('body');
            $table->string('locale', 2)->default('ar');
            // new, read, archived
            $table->string('status', 10)->default('new');
            $table->boolean('is_spam')->default(false);
            $table->text('reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
