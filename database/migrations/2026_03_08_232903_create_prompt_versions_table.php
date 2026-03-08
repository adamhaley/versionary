<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('prompt_id');
            $table->unsignedInteger('version_number');
            $table->string('branch_name')->default('main');
            $table->uuid('parent_version_id')->nullable();
            $table->string('title')->nullable();
            $table->text('system_prompt')->nullable();
            $table->text('user_prompt_template')->nullable();
            $table->text('developer_prompt')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_restored')->default(false);
            $table->uuid('restored_from_version_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->foreign('prompt_id')->references('id')->on('prompts')->cascadeOnDelete();
            $table->unique(['prompt_id', 'version_number', 'branch_name']);
            $table->index(['prompt_id', 'branch_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_versions');
    }
};
