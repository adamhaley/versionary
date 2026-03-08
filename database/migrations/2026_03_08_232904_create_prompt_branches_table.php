<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_branches', function (Blueprint $table) {
            $table->id();
            $table->uuid('prompt_id');
            $table->string('name');
            $table->uuid('base_version_id');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('prompt_id')->references('id')->on('prompts')->cascadeOnDelete();
            $table->unique(['prompt_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_branches');
    }
};
