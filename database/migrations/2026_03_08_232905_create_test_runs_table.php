<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_runs', function (Blueprint $table) {
            $table->id();
            $table->uuid('prompt_id');
            $table->uuid('prompt_version_id');
            $table->foreignId('adapter_id')->constrained('adapters')->restrictOnDelete();
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('rendered_request')->nullable();
            $table->json('raw_response')->nullable();
            $table->text('normalized_output')->nullable();
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->text('error_message')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->unsignedInteger('token_usage_input')->nullable();
            $table->unsignedInteger('token_usage_output')->nullable();
            $table->decimal('estimated_cost', 10, 6)->nullable();
            $table->timestamps();

            $table->foreign('prompt_id')->references('id')->on('prompts')->cascadeOnDelete();
            $table->foreign('prompt_version_id')->references('id')->on('prompt_versions')->cascadeOnDelete();
            $table->index(['prompt_id', 'prompt_version_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_runs');
    }
};
