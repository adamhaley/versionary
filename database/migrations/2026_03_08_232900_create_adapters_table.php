<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adapters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category'); // coding, chat, image, video
            $table->string('provider');
            $table->string('driver_class')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('supports_streaming')->default(false);
            $table->boolean('supports_system_prompt')->default(false);
            $table->boolean('supports_images')->default(false);
            $table->boolean('supports_video')->default(false);
            $table->boolean('supports_code')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adapters');
    }
};
