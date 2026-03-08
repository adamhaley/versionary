<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('prompt_id');
            $table->string('category'); // coding, chat, image, video
            $table->foreign('prompt_id')->references('id')->on('prompts')->cascadeOnDelete();
            $table->unique(['prompt_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_categories');
    }
};
