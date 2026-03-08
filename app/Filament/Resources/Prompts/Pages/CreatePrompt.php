<?php

namespace App\Filament\Resources\Prompts\Pages;

use App\EventSourcing\Aggregates\PromptAggregate;
use App\Filament\Resources\Prompts\PromptResource;
use App\Models\Prompt;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreatePrompt extends CreateRecord
{
    protected static string $resource = PromptResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        $promptId = (string) Str::uuid();
        $userId = auth()->id();

        PromptAggregate::retrieve($promptId)->create(
            name: $data['name'],
            slug: $data['slug'],
            summary: $data['summary'] ?? null,
            categories: $data['categories'] ?? [],
            createdBy: $userId,
            title: $data['title'] ?? null,
            systemPrompt: $data['system_prompt'] ?? null,
            userPromptTemplate: $data['user_prompt_template'] ?? null,
            developerPrompt: $data['developer_prompt'] ?? null,
            notes: $data['notes'] ?? null,
        )->persist();

        return Prompt::findOrFail($promptId);
    }
}
